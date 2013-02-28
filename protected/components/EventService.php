<?php



/**
 * Сервис по работе с событиями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventService {
    
    /**
     * Поставить событие в очередь
     * 
     * @param mixed $simId
     * @param mixed $eventId
     * @param string $triggerTime
     */
    public static function addToQueue($simId, $eventId, $triggerTime) {
        $eventTriggers = new EventTrigger();
        $eventTriggers->sim_id = $simId;
        $eventTriggers->event_id = $eventId;
        $eventTriggers->trigger_time = $triggerTime;
        $eventTriggers->insert();
    }

    /**
     * Добавить событие в симуляцию по коду
     * @param string $code
     * @param int $simId
     * @param bool $eventTime
     * @return bool
     */
    public static function addByCode($code, $simId, $eventTime = false) {
        if ( ($code == '') || ($code == '-') ) return false;
        if ($code == 'T') return false; // финальная реплика
        
        // проверить есть ли событие по такому коду и если есть то создать его
        $event = EventSample::model()->byCode($code)->find();
        if ($event) {
            // попробуем вытащить delay из диалога
            if ($eventTime) {
                $dialog = DialogService::getFirstReplicaByCode($code);

                if ($dialog) {
                    if ($dialog->delay > 0) { //TODO:Проблемное место
                        //Logger::write('$dialog->duration = '.$dialog->duration);
                        //Logger::write('$eventTime = '.$eventTime);
                        $eventTime = GameTime::addMinutesTime($eventTime, $dialog->delay);
                        //Logger::write('$eventTime = '.$eventTime);
                        //Logger::write("=============");
                    }
                }
            }
            
            
            if (!$eventTime) $eventTime = $event->trigger_time;
            
            // проверим а есть ли такой триггер
            $eventsTriggers = EventTrigger::model()->bySimIdAndEventId($simId, $event->id)->find();
            if ($eventsTriggers) {
                $eventsTriggers->trigger_time = $eventTime; 
                $eventsTriggers->save();
                return true;
            }
            
            $eventsTriggers = new EventTrigger();
            $eventsTriggers->sim_id         = $simId;
            $eventsTriggers->event_id       = $event->id;
            $eventsTriggers->trigger_time   = $eventTime; 
            $eventsTriggers->save();
        }
    }
    
    public static function deleteByCode($code, $simId) {
        $event = EventSample::model()->byCode($code)->find();
        if (!$event) return false; // нет у нас такого события
        
        $eventsTriggers = EventTrigger::model()->bySimIdAndEventId($simId, $event->id)->find();
        if (!$eventsTriggers) return false;
        $eventsTriggers->delete();
        return true;
    }
    
    public static function isDialog($code) {
        return preg_match_all("/E(.*)+/", $code, $matches);
    }

    /**
     * Обработка связанных сущностей типа почты, плана...
     * @param $eventCode
     * @param $simId
     * @throws Exception
     * @return array
     */
    public static function processLinkedEntities($eventCode, $simId) {
        $simulation = Simulation::model()->findByPk($simId);
        // анализ писем
        $code = false;
        $type = false;
        
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
            // если входящее письмо не пришло (кодировка M) - то указанное письмо должно прийти
            $mailModel = MailBoxService::copyMessageFromTemplateByCode($simId, $code);
            if (!$mailModel) {
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
            $documentTemplate = DocumentTemplate::model()->byCode($code)->find();
            if (!$documentTemplate) return false;
            $templateId = $documentTemplate->id;
            
            $document = MyDocument::model()->byTemplateId($templateId)->bySimulation($simId)->find();
            if (!$document) return false;
            
            return array('result' => 1, 'eventType' => $type, 'data' => ['id' => $document->id]);
        }
        
        if ($type == 'P') {
            $task = Task::model()->findByAttributes(['code' => $code]);

            // Example of how not to do:
            // if (!$task) return false;
            // Example of how to do:
            assert($task);
            
            TodoService::add($simulation, $task);
            
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
        $events = EventSample::model()->findAll();
        $list = array();
        foreach($events as $event) {
            $list[$event->code] = $event->id;
        }
        
        return $list;
    }
    
    public static function getReplicaByCode($eventCode, $simId) {
        $dialogs = Replica::model()->byCode($eventCode)->byStepNumber(1)->findAll();
            
        $data = array();
        foreach($dialogs as $dialog) {
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
     * @param $replica Replica
     * @param $simId
     * @internal param string $code
     * @return boolean
     */
    public static function allowToRun($replica, $simId) {
        $rules = FlagBlockReplica::model()->findAllByAttributes([
            'replica_id' => $replica->excel_id
        ]);

        if (NULL === $rules) {
            return true; // нет правил для данного события
        }    

        $simulationFlags = SimulationService::getFlags($simId);
        if (count($simulationFlags) == 0) {
            // todo: event that allowed when FXX = 0 is possible, but this method will return false
            return false; // у нас пока нет установленных флагов - не чего сравнивать
        }    
        
        // проверить на совпадение флагов с теми что есть в симуляции
        $result = FlagsService::compareFlags($simulationFlags, $rules);
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
    
    /**
     * @return mixed array
     */
    public static function getEventsListForAdminka()
    {
        $events = array();
        
        $codes = array();
        foreach (EventSample::model()->findAll() as $event) {
            if (false === in_array($event->code, $codes)) {
                $codes[] = $event->code;
                $events[] = array(
                    'id'    => $event->id,
                    'cell'  => array(
                        $event->id, 
                        $event->code, 
                        $event->title,
                        (7 == $event->on_ignore_result) ? "нет результата" : $event->on_ignore_result,
                        (1 == $event->on_hold_logic) ? "ничего" : $event->on_hold_logic  // current import set this value
                    )
                );
            }
        }
        
        return $events;
    }
}


