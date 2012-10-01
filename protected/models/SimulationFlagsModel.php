<?php


/**
 * Description of SimulationFlagsModel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationFlagsModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return SimulationFlagsModel 
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
            return 'simulation_flags';
    }
    
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    public function byFlag($flag)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "flag = '{$flag}'"
        ));
        return $this;
    }
}

?>
