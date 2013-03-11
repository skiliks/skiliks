<?php



/**
 * Используется для логирования состояния плана.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanLog extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * user id
     * @var integer
     */
    public $uid; 
    
    /**
     * @var integer
     */
    public $snapshot_date; // ??
    
    /**
     * @var integer
     */
    public $day; // of game? 1,2?
    
    
    /**
     * tasks.id
     * @var int
     */
    public $task_id;
    
    /**
     * @var integer
     */
    public $snapshot_time; // 1 - at 11:00, 2 - when simStop
    
    /**
     * simulations.id
     * @var int
     */
    public $sim_id;
    
    /**
     * @var int
     */
    public $todo_count;    
    
    
    /**
     *
     * @param type $className
     * @return DayPlanLog
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
            return 'day_plan_log';
    }

    public function relations()
    {
        return [
            'task' => [self::BELONGS_TO, 'Task', 'task_id']
        ];
    }

    /**
     * Выборка по симуляции
     * 
     * @param int $simId
     * @return DayPlanLog
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
}


