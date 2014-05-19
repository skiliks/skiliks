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

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param string $className
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


