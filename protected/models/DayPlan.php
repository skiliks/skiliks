<?php



/**
 * Модель дневного плана. Хранит состояние дневного плана для каждой симуляии пользователя.
 * 
 * Связана с моделями: Simulations, Tasks.
 *
 * @property int sim_id
 * @property int task_id
 * @property string date
 * @property int day
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlan extends CActiveRecord
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
     * In minutes from 00:00 game day
     * @var int
     */
    public $date;
    
    /**
     * 1 -first, 2 - second
     * @var int
     */
    public $day;
    
    /**
     * tasks.id
     * @var int
     */
    public $task_id;   
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
   /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return DayPlan 
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
            return 'day_plan';
    }
    
    /**
     * Выбрать по диапазону дат от и до
     * @param int $from
     * @param int $to
     * @return DayPlan 
     */
    public function byDate($from, $to)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "date >= $from and date <= $to"
        ));
        return $this;
    }
    
    /**
     * Выбрать для заданной симуляции
     * @param int $simId
     * @return DayPlan 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id={$simId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданной задачи
     * @param int $taskId
     * @return DayPlan 
     */
    public function byTask($taskId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "task_id={$taskId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать ближайшую запись по дате от и до
     * @param int $from
     * @param int $to
     * @return DayPlan 
     */
    public function nearest($from, $to)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "date > '{$from}' and date < '{$to}'"
        ));
        return $this;
    }
}


