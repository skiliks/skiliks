<?php



/**
 * Длительность диалогов в рамках конкретной симуляции. Используется при расчете оценки.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationDialogDuration extends CActiveRecord
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
     * in game minutes
     * @var integer
     */
    public $duration;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'simulations_dialogs_durations';
    }
    
    /**
     * Выбрать согласно заданной симуляции
     * @param int $simId
     * @return SimulationDialogDuration
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'sim_id = '.(int)$simId
        ));
        return $this;
    }
}


