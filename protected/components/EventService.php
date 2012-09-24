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
    public static function addByCode($code, $simId) {
        if ( ($code == '') || ($code == '-') ) return false;
        
        // проверить есть ли событие по такому коду и если есть то создать его
        $event = EventsSamples::model()->byCode($code)->find();
        if ($event) {
            $eventsTriggers = new EventsTriggers();
            $eventsTriggers->sim_id         = $simId;
            $eventsTriggers->event_id       = $event->id;
            $eventsTriggers->trigger_time   = $event->trigger_time; 
            $eventsTriggers->save();
        }
    }
}

?>
