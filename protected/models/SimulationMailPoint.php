<?php



/**
 * Оценки, набранные за использование почтой в рамках конкретной симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationMailPoint extends CActiveRecord
{
    public $id;
    
    public  $sim_id;
    
    public $point_id;
    
    public $scale_type_id;
    
    public $value;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
            
    /**
     *
     * @param type $className
     * @return SimulationMailPoint 
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
            return 'simulations_mail_points';
    }
    
    /**
     * Выбрать согласно заданной симуляции
     * @param int $simId
     * @return SimulationMailPoint 
     */
    public function bySimulation($simId)
    {
        $simId = (int)$simId;
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    /** 
     * Выбрать согласно заданному письму
     * @param int $mailId
     * @return SimulationMailPoint 
     */
    public function byMail($mailId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id = {$mailId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданной оценке
     * @param int $pointId
     * @return SimulationMailPoint 
     */
    public function byPoint($pointId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "point_id = {$pointId}"
        ));
        return $this;
    }
}


