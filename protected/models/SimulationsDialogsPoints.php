<?php


/**
 * Оценки, набранные в ходе выбора диалоговых реплик в рамках конкретной симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationsDialogsPoints extends CActiveRecord
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
     * character_points_titles.id
     * @var integer
     */
    public $point_id; 
    
    /**
     * @var integer
     */
    public $count;
    
    /**
     * @var float
     */
    public $value;    
    
    /**
     * @var integer
     */
    public $count6x;
    
    /**
     * @var float
     */
    public $value6x;    
    
    /**
     * @var integer
     */
    public $count_negative;
    
    /**
     * @var float
     */
    public $value_negative;    
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return SimulationDialogPoint
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
            return 'simulations_dialogs_points';
    }
    
    /**
     * Выбрать согласно заданной симуляции и оценке
     * @param int $simId
     * @param int $pointId
     * @return SimulationDialogPoint
     */
    public function bySimulationAndPoint($simId, $pointId)
    {
        $simId = (int)$simId;
        $pointId = (int)$pointId;
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId} and point_id = {$pointId}"
        ));
        return $this;
    }
}


