<?php



/**
 * Список сделать в рамках конкретной симуляции
 * 
 * Связана с моделями:  Simulations, Tasks.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Todo extends CActiveRecord
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
     * tasks.id
     * @var int
     */
    public $task_id;
    
    /**
     * game minutes
     * @var integer
     */
    public $adding_date;//TODO:Не понятно зачем оно, это просто реальная дата на сервере!!
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
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
    
    /**
     * Выбрать в рамках заданной симуляции
     * @param int $simId
     * @return Todo 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id={$simId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданной задаче
     * @param int $taskId
     * @return Todo 
     */
    public function byTask($taskId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "task_id={$taskId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать самую свежую задачу
     * @return Todo 
     */
    public function byLatestAddingDate()
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "adding_date desc"
        ));
        return $this;
    }
}


