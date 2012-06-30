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
}

?>
