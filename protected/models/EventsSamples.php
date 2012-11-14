<?php



/**
 * Содержит список событий, которые есть в системе.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsSamples extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return EventsSamples 
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
     * Ограничить выборку записей
     * @param int $limit
     * @return EventsSamples 
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
     * @return EventsSamples 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code='$code'"
        ));
        return $this;
    }
    
    /**
     * Выбрать событие по коду с учетом like.
     * @param string $code
     * @return EventsSamples 
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
     * @return EventsSamples 
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
     * @return EventsSamples 
     */
    public function nearest($fromTime, $toTime)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "trigger_time >= $fromTime and trigger_time <= $toTime"
        ));
        return $this;
    }
}

?>
