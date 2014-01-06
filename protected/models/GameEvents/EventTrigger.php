<?php
/**
 * По сути очередь событий, которая используется в рамках симуляции. 
 * Именно здесь хранится информация какое событие и когда должно произойти в 
 * рамках конкретной симуляции.
 *
 * @property integer $id
 * @property integer $sim_id
 * @property string  $event_id
 * @property string  $trigger_time, game minutes, time when event must be scheduled in game during sim start
 * @property bool    $force_run
 *
 * @property EventSample $event_sample
 */
class EventTrigger extends CActiveRecord
{
    /**
     * Вернуть ближайшее событие
     * @param $simId
     * @param $triggerTime
     * @return EventTrigger
     */
    public function nearestOne($simId, $triggerTime)
    {
        $condition = new CDbCriteria();
        $condition->compare('t.trigger_time', '<=' . $triggerTime);
        $condition->compare('sim_id', $simId);
        $condition->addCondition('t.trigger_time IS NOT NULL');
        $condition->addCondition('t.trigger_time != "00:00:00" ');
        $condition->order = 'CASE WHEN event.code LIKE "P%" THEN 1 WHEN event.code LIKE "M%" THEN 2 ELSE 3 END, '.
            't.trigger_time';
        $condition->join = 'JOIN event_sample event ON event.id=t.event_id';
        $condition->limit = 1;

        $this->getDbCriteria()->mergeWith($condition);
        $this->getDbCriteria();

        return $this;
    }

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     * @param string $className
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

    public function relations()
    {
        return [
            'event_sample' => [self::BELONGS_TO, 'EventSample', 'event_id']
        ];
    }
}


