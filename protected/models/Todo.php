<?php



/**
 * Список сделать в рамках конкретной симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Todo extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return Todo 
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
            return 'todo';
    }
    
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id={$simId}"
        ));
        return $this;
    }
    
    public function byTask($taskId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "task_id={$taskId}"
        ));
        return $this;
    }
    
    public function byLatestAddingDate()
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "adding_date desc"
        ));
        return $this;
    }
}

?>
