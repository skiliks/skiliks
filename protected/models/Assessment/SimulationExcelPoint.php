<?php
/**
 * @param integer $id
 * @param integer $sim_id
 * @param float $value
 * @param integer $formula_id
 */
class SimulationExcelPoint extends CActiveRecord
{
    // Place your specific code there

    // ---------------------------------------------------------------------------------------------------

    /**
     * Выбрать по заданной формуле
     * @param int $formulaId
     * @return SimulationExcelPoint
     */
    public function byFormula($formulaId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "formula_id = {$formulaId}"
        ));
        return $this;
    }

    /**
     * Выбрать по заданной формуле
     * @param int $formulaId
     * @return SimulationExcelPoint
     */
    public function byExistsValue()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "value != 0"
        ));
        return $this;
    }
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return SimulationExcelPoint
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
}


