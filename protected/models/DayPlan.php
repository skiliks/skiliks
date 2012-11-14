<?php



/**
 * Модель дневного плана. Хранит состояние дневного плана для каждой симуляии пользователя.
 *
 * @property int sim_id
 * @property int task_id
 * @property string date
 * @property int day
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlan extends CActiveRecord{

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
    
    public function byDate($from, $to)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "date >= $from and date <= $to"
        ));
        return $this;
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
    
    public function nearest($from, $to)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "date > {$from} and date < {$to}"
        ));
        return $this;
    }
}

?>
