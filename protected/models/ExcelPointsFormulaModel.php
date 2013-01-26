<?php



/**
 * Содержит набор формул для расчета оценки по Excel.
 * 
 * @deprecated
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelPointsFormulaModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return ExcelPointsFormulaModel 
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
            return 'excel_points_formula';
    }
}


