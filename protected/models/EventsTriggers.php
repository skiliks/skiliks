<?php


/**
 * Description of EventsTriggers
 *
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
     * @return EventsTriggers 
     */
    public function nearest($simId, $triggerTime)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "trigger_time <= {$triggerTime} and sim_id={$simId}",
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
}

?>
