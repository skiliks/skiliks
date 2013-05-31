<?php


/**
 * По сути очередь событий, которая используется в рамках симуляции. 
 * Именно здесь хранится информация какое событие и когда должно произойти в 
 * рамках конкретной симуляции.
 *
 * @property int $sim_id
 * @property mixed $event_id
 * @property mixed $trigger_time
 * @property bool $force_run
 *
 * @property EventSample $event_sample
 */
class EventTrigger extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * simulations.id
     * @var int
     */
    public $sim_id;
    
    /**
     * event_samples.id
     * @var integer
     */
    public $event_id;
    
    /**
     * In game minutes, time when event must be sheduled in game during sim start
     * @var integer
     */
    public $trigger_time;      
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return EventTrigger
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
     * @return EventTrigger
     */
    public function nearest($simId, $triggerTime)
    {
        $condition = new CDbCriteria();
        $condition->compare('t.trigger_time', '<=' . $triggerTime);
        $condition->compare('sim_id', $simId);
        $condition->addCondition('t.trigger_time IS NOT NULL');
        $condition->addCondition('t.trigger_time != "00:00:00" ');
        $condition->order = 'IF(event.code like "M%", "00:00:00", t.trigger_time) ASC';
        $condition->join = 'JOIN event_sample event ON event.id=t.event_id';
        $this->getDbCriteria()->mergeWith($condition);

        return $this;
    }
    
    /**
     * Выбрать заданное событие в рамках заданной симуляции
     * @param int $simId
     * @param int $eventId
     * @return EventTrigger 
     */
    public function bySimIdAndEventId($simId, $eventId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = $simId and event_id = $eventId",
            'limit' => 1,
        ));
        return $this;
    }
    
    /**
     * Выбрать событие заданное по коду в рамках симуляции
     * @param int $simId
     * @param string $eventCode
     * @return EventTrigger 
     */
    public function bySimIdAndEventCode($simId, $eventCode)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = $simId and code = '$eventCode'",
            'limit' => 1,
        ));
        return $this;
    }
    
    /**
     * Выбрать по идентификатору события
     * @param int $eventId
     * @return EventTrigger 
     */
    public function byEvent($eventId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "event_id = $eventId",
            'limit' => 1,
        ));
        return $this;
    }

    public function relations()
    {
        return [
            'event_sample' => [self::BELONGS_TO, 'EventSample', 'event_id']
        ];
    }
}


