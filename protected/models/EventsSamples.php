<?php



/**
 * Description of EventsSamples
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsSamples extends CActiveRecord{
    
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
    
    public function limit($limit = 5)
    {
        $this->getDbCriteria()->mergeWith(array(
            'limit' => $limit,
            'offset' => 0
        ));
        return $this;
    }
    
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code='$code'"
        ));
        return $this;
    }
    
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id='$id'"
        ));
        return $this;
    }
}

?>
