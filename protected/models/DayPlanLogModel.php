<?php



/**
 * Используется для логирования состояния плана.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanLogModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return DayPlanLogModel 
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
    
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
}

?>
