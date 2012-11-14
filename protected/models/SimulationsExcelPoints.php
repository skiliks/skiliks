<?php



/**
 * Оценки набранные за Excel в результате проверки конктрольных формул
 * в рамках конкретной симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationsExcelPoints extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return SimulationsExcelPoints 
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
            return 'simulations_excel_points';
    }
    
    public function bySimulation($simId)
    {
        $simId = (int)$simId;
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    public function byFormula($formulaId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "formula_id = {$formulaId}"
        ));
        return $this;
    }
}

?>
