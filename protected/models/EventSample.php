<?php



/**
 * Содержит список событий, которые есть в системе.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventSample extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * Replica code, 'ET1.1', 'E8' ...
     * @var string
     */
    public $code;
    
    /**
     * @var string
     */
    public $title;
    
    /**
     * @var integer
     */
    public $on_ignore_result; // look not in use
    
    /**
     * @var integer
     */
    public $on_hold_logic; // ?
    
    /**
     * In game minutes, time when event must be sheduled in game during sim start
     * @var integer
     */
    public $trigger_time;

    /**
     * @var string
     */
    public $import_id;

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return EventSample 
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
            return 'events_samples';
    }
    
    /**
     * @param string $ids
     * @return array of \EventSamples
     */
    public function byIdsNotIn($ids)
    {
        if ($ids) {
            $this->getDbCriteria()->mergeWith(array(
                'condition' => " `id` NOT IN ({$ids}) "
            ));
        }
        return $this;
    }    
    
    /**
     * Ограничить выборку записей
     * @param int $limit
     * @return EventSample 
     */
    public function limit($limit = 5)
    {
        $this->getDbCriteria()->mergeWith(array(
            'limit' => $limit,
            'offset' => 0
        ));
        return $this;
    }
    
    /**
     * Выбрать события по коду
     * @param string $code
     * @return EventSample 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code='$code'"
        ));
        return $this;
    }
    
    /**
     * @return EventSample 
     */
    public function byNotDocumentCode()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code NOT LIKE 'D%'"
        ));
        return $this;
    }    
    
    /**
     * @return EventSample 
     */
    public function byTriggerTimeGreaterThanZero()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "trigger_time is not null AND code != ''"
        ));
        return $this;
    }    
    
    /**
     * @return EventSample 
     */
    public function byNotPlanTaskCode()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code NOT LIKE 'P%'"
        ));
        return $this;
    }    
    
    /**
     * @return EventSample 
     */
    public function byNotSentTodayEmailCode()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code NOT LIKE 'MS%'"
        ));
        return $this;
    }    
    
    /**
     * @return EventSample 
     */
    public function byNotTerminatorCode()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code != 'T'"
        ));
        return $this;
    }    
    
    /**
     * @return EventSample 
     */
    public function byNotSentYesterdayEmailCode()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code NOT LIKE 'MY%'"
        ));
        return $this;
    }    
    
    /**
     * Выбрать событие по коду с учетом like.
     * @param string $code
     * @return EventSample 
     */
    public function likeCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code like '$code' "
        ));
        return $this;
    }
    
    /**
     * Выбрать событие по id
     * @param int $id
     * @return EventSample 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id=".(int)$id
        ));
        return $this;
    }
    
    /**
     * Выбрать событие, попадающее в заданный интервал времени
     * @param int $fromTime от
     * @param int $toTime до
     * @return EventSample 
     */
    public function nearest($fromTime, $toTime)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "trigger_time >= $fromTime and trigger_time <= $toTime"
        ));
        return $this;
    }
}


