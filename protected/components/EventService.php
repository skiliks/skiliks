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
        
        Logger::debug("add event : code {$code} time : {$eventTime}");
        // проверить есть ли событие по такому коду и если есть то создать его
        $event = EventsSamples::model()->byCode($code)->find();
        if ($event) {
            if (!$eventTime) $eventTime = $event->trigger_time;
            
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
            //$mailModel = MailBoxModel::model()->byCode($code)->find();
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
            // проверим есть ли такая задача у нас в туду
            $todo = Todo::model()->bySimulation($simId)->byTask($task->id)->find();
            if (!$todo) {
                $todo = new Todo();
                $todo->sim_id = $simId;
                $todo->task_id = $task->id;
                $todo->insert();
            }
            
            return array('result' => 1, 'eventType' => $type, 'id' => $task->id);
        }
        
        return $result;
    }
}

?>
