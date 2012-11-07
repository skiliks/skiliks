<?php


/**
 * Description of EventsTriggers
 *
 * @property int sim_id
 * @property mixed event_id
 * @property mixed trigger_time
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsTriggers extends CActiveRecord{

    /**
     *
     * @param type $className
     * @return EventsTriggers
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'events_triggers';
    }

    /**
     * Вернуть ближайшее событие
     * @param $simId
     * @param $triggerTime
     * @return EventsTriggers
     */
    public function nearest($simId, $triggerTime)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "trigger_time <= {$triggerTime} and sim_id={$simId} and trigger_time != 0",
            'order' => "trigger_time asc",       
            //'limit' => 1,
        ));
        return $this;
    }
    
    public function bySimIdAndEventId($simId, $eventId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = $simId and event_id = $eventId",
            'limit' => 1,
        ));
        return $this;
    }
    
    public function bySimIdAndEventCode($simId, $eventCode)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = $simId and code = '$eventCode'",
            'limit' => 1,
        ));
        return $this;
    }
    
    public function byEvent($eventId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "event_id = $eventId",
            'limit' => 1,
        ));
        return $this;
    }
}

?>
