<?php



/**
 * Сервис по работе с событиями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventService {
    
    /**
     * Получить текущее событие в заданной симуляции
     * 
     * @param type $simId 
     */
    public static function getCurrent($simId) {
        $eventsStates = EventsStates::model()->bySimulation($simId)->find();
        if (!$eventsStates) return false;
        
        return $eventsStates->event_id;
    }
    
    /**
     * Поставить событие в очередь
     * 
     * @param type $simId
     * @param type $eventId
     * @param type $triggerTime 
     */
    public static function addToQueue($simId, $eventId, $triggerTime) {
        $eventTriggers = new EventsTriggers();
        $eventTriggers->sim_id = $simId;
        $eventTriggers->event_id = $eventId;
        $eventTriggers->trigger_time = $triggerTime;
        $eventTriggers->insert();
    }
    
    /**
     * Добавить событие в симуляцию по коду
     * @param string $code
     * @param int $simId
     * @return type 
     */
    public static function addByCode($code, $simId, $eventTime = false) {
        if ( ($code == '') || ($code == '-') ) return false;
        if ($code == 'T') return false; // финальная реплика
        
        //Logger::debug("add event by code : $code time : $eventTime");
        // проверить есть ли событие по такому коду и если есть то создать его
        $event = EventsSamples::model()->byCode($code)->find();
        if ($event) {
            // попробуем вытащить delay из диалога
            if ($eventTime) {
                //if (DialogService::existByCode($code)) {
                    $dialog = DialogService::getFirstReplicaByCode($code);
                    if ($dialog) {
                        if ($dialog->duration > 0) {
                            Logger::debug("add durarion : {$dialog->duration}");
                            $eventTime+=$dialog->duration;
                        }
                    }
                //}
            }
            
            
            if (!$eventTime) $eventTime = $event->trigger_time;
            
            Logger::debug("add event : code {$code} time : {$eventTime}");
            
            // проверим а есть ли такой триггер
            $eventsTriggers = EventsTriggers::model()->bySimIdAndEventId($simId, $event->id)->find();
            if ($eventsTriggers) {
                $eventsTriggers->trigger_time = $eventTime; 
                $eventsTriggers->save();
                return true;
            }
            
            $eventsTriggers = new EventsTriggers();
            $eventsTriggers->sim_id         = $simId;
            $eventsTriggers->event_id       = $event->id;
            $eventsTriggers->trigger_time   = $eventTime; 
            $eventsTriggers->save();
        }
    }
    
    public static function deleteByCode($code, $simId) {
        $event = EventsSamples::model()->byCode($code)->find();
        if (!$event) return false; // нет у нас такого события
        
        $eventsTriggers = EventsTriggers::model()->bySimIdAndEventId($simId, $event->id)->find();
        if (!$eventsTriggers) return false;
        $eventsTriggers->delete();
        return true;
    }
    
    public static function isDialog($code) {
        return preg_match_all("/E(.*)+/", $code, $matches);
    }
    
    /**
     * Обработка связанных сущностей типа почты, плана...
     * @param type $dialog
     * @return type 
     */
    public static function processLinkedEntities($eventCode, $simId) {
        // анализ писем
        $code = false;
        $type = false;
        
        Logger::debug("_processLinkedEntities : code : {$eventCode}");
        
        // логирование плана
        if ($eventCode == '#plog') {
            DayPlanLogger::log($simId);
            return false;
        }
        
        if (preg_match_all("/MY(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'MY'; // Message Yesterday
        }
        
        if (preg_match_all("/M(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'M'; // входящие письма
        }
        
        if (preg_match_all("/MSY(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'MSY'; // входящие письма
        }
        
        if (preg_match_all("/MS(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'MS'; // входящие письма
        }
        
        if (preg_match_all("/D(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'D'; // документ
        }
        
        if (preg_match_all("/P(\d+)/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'P'; // задача в плане
        }
        
        if (preg_match_all("/^T$/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'T'; // окончательная реплика
        }
        
        if (!$code) return false; // у нас нет связанных сущностей
        
        $result = false;
        if ($type == 'MY') {
            // отдать письмо по коду
            $mailModel = MailBoxModel::model()->byCode($code)->find();
            if ($mailModel) {
                // если входящее письмо УЖЕ пришло (кодировка MY - Message Yesterday)
                //  - то в списке писем должно быть выделено именно это письмо
                return array('result' => 1, 'id' => $mailModel->id, 'eventType' => $type);
            }
        }
        
        if ($type == 'M') {
            Logger::debug("Send message by code : $code");
            // если входящее письмо не пришло (кодировка M) - то указанное письмо должно прийти
            $mailModel = MailBoxService::copyMessageFromTemplateByCode($simId, $code);
            if (!$mailModel) {
                Logger::debug("cant copy mail by code $code");
                throw new Exception("cant copy mail by code $code");
            }
            
            if ($mailModel) {
                $mailModel->group_id = 1; //входящие
                $mailModel->save();
                return array('result' => 1, 'id' => $mailModel->id, 'eventType' => $type);
            }
        }
        
        if ($type == 'MSY') {
            // отдать письмо по коду
            $mailModel = MailBoxModel::model()->byCode($code)->find();
            if ($mailModel) {
                // если исходящее письмо уже отправлено  (кодировка MSY - Message Sent Yesterday)
                //  - то в списке писем должно быть выделено именно это письмо
                return array('result' => 1, 'id' => $mailModel->id, 'eventType' => $type);
            }
        }
        
        if ($type == 'MS') {
            // если исходящее письмо не отправлено  (кодировка MS - Message Sent) - то должно открыться окно написания нового письма
            return array('result' => 1, 'eventType' => $type);
        }
        
        if ($type == 'D') {
            // определить документ по коду
            $documentTemplateModel = MyDocumentsTemplateModel::model()->byCode($code)->find();
            if (!$documentTemplateModel) return false;
            $templateId = $documentTemplateModel->id;
            
            $document = MyDocumentsModel::model()->byTemplateId($templateId)->bySimulation($simId)->find();
            if (!$document) return false;
            
            return array('result' => 1, 'eventType' => $type, 'id' => $document->id);
        }
        
        if ($type == 'P') {
            $task = Tasks::model()->byCode($code)->find();
            if (!$task) return false;
            
            TodoService::add($simId, $task->id);
            
            return array('result' => 1, 'eventType' => $type, 'id' => $task->id);
        }
        
        if ($type == 'T') {
            return array('result' => 1, 'eventType' => 1);
        }
        
        return $result;
    }
    
    /**
     * Получение кодов всех событий
     */
    public static function getAllCodesList() {
        $events = EventsSamples::model()->findAll();
        $list = array();
        foreach($events as $event) {
            $list[$event->code] = $event->id;
        }
        
        return $list;
    }
    
    public static function getReplicaByCode($eventCode, $simId) {
        $dialogs = Dialogs::model()->byCode($eventCode)->byStepNumber(1)->findAll();
            
        $data = array();
        foreach($dialogs as $dialog) {
            
            
            Logger::debug("check dialog by code : {$dialog->code} next event : {$dialog->next_event_code}");

            // Если у нас реплика к герою
            if ($dialog->replica_number == 0) {
                // События типа диалог мы не создаем
                if (!EventService::isDialog($dialog->next_event_code)) {
                    // создадим событие
                    EventService::addByCode($dialog->next_event_code, $simId, SimulationService::getGameTime($simId));
                }
            }
            $data[] = DialogService::dialogToArray($dialog);
        }

        if (isset($data[0]['ch_from'])) {
            $characterId = $data[0]['ch_from'];
            $character = Characters::model()->byId($characterId)->find();
            if ($character) {
                $data[0]['title'] = $character->title;
                $data[0]['name'] = $character->fio;
            }
        }
        
        return $data;
    }
    
    /**
     * Проверяет а можем ли мы запускать это событие
     * @param string $code 
     * @return true
     */
    public static function allowToRun($code, $simId, $stepNumber = false, $replicaNumber = false) {
        Logger::debug("try to get flags rules for code : $code stepNumber: $stepNumber replicaNumber $replicaNumber");
        $ruleModel = FlagsService::getRuleByCode($code, $stepNumber, $replicaNumber);
        if (!$ruleModel) {
            Logger::debug("no rules for event : $code");
            return true; // нет правил для данного события
        }    
        
        // получим флаги для этого правила
        $flags = FlagsService::getFlags($ruleModel->id);
        if (count($flags) == 0) {
            Logger::debug("no flags for rule");
            return true; // для данного кода нет правил
        }    
        
        // получить флаги в рамках симуляции
        $simulationFlags = SimulationService::getFlags($simId);
        if (count($simulationFlags)==0) {
            Logger::debug("no simulation flags");
            return false; // у нас пока нет установленных флагов - не чего сравнивать
        }    
        
        Logger::debug("start compare flags  s: ".var_export($simulationFlags, true).' r :'.var_export($flags, true));
        // проверить на совпадение флагов с теми что есть в симуляции
        $result = FlagsService::compareFlags($simulationFlags, $flags);
        Logger::debug("compare result : ".var_export($result, true));
        return $result;
    }
    
    public static function isPlan($eventCode) {
        return preg_match("/P(\d+)/", $eventCode);
    }
    
    public static function isDocument($eventCode) {
        return preg_match("/D(\d+)/", $eventCode);
    }
    
    public static function isAnyMail($eventCode) {
        return preg_match("/M(.*)/", $eventCode);
    }
    
    public static function isSendedMail($eventCode) {
        return preg_match("/MS(\d+)/", $eventCode);
    }
    
    public static function isMessageYesterday($eventCode) {
        return preg_match("/MY(\d+)/", $eventCode);
    }
}


