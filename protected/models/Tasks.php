<?php



/**
 * Модель задач
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Tasks extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return Tasks 
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
            return 'tasks';
    }
    
    public function byIds($ids)
    {
        
        $ids = implode(',', $ids);
        
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$ids})"
        ));
        return $this;
    }
    
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
    
    
    
    public function byStartType($startType)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "start_type = '{$startType}'"
        ));
        return $this;
    }
}

?>
