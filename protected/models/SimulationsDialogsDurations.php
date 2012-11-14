<?php



/**
 * Длительность диалогов в рамках конкретной симуляции. Используется при расчете оценки.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationsDialogsDurations extends CActiveRecord{
    
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
    
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'sim_id = '.(int)$simId
        ));
        return $this;
    }
}

?>
