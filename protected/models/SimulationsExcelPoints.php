<?php



/**
 * Оценки набранные за Excel в результате проверки конктрольных формул
 * в рамках конкретной симуляции
 * 
 * Связана с моделями:  Simulations, ExcelPointsFormulaModel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationsExcelPoints extends CActiveRecord
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
     * @var float
     */
    public $value;
    
    /**
     * @var integer
     */
    public $formula_id;    
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
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
    
    /**
     * Выбрать по заданной симуляции
     * @param int $simId
     * @return SimulationsExcelPoints 
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
     * Выбрать по заданной формуле
     * @param int $formulaId
     * @return SimulationsExcelPoints 
     */
    public function byFormula($formulaId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "formula_id = {$formulaId}"
        ));
        return $this;
    }
}


