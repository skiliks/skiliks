<?php


/**
 * Оценки, набранные в ходе выбора диалоговых реплик в рамках конкретной симуляции
 * 
 * Связана с моделями:  Simulations, CharactersPointsTitles
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationsDialogsPoints extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return SimulationsDialogsPoints 
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
     * @return SimulationsDialogsPoints 
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

?>
