<?php



/**
 * Хранит состояние после отпуска для каждой симуляции.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanAfterVacation extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * tasks.id
     * @var int
     */
    public $task_id; 
    
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
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'day_plan_after_vacation';
    }
    
    /**
     * Выборка по симуляции
     * 
     * @param int $simId
     * @return DayPlanAfterVacation 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id={$simId}"
        ));
        return $this;
    }
}


