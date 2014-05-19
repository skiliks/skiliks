<?php
/**
 * Содержит набор формул для расчета оценки по Excel.
 *
 * @property integer $id
 * @property string  $formula
 */
class ExcelPointFormula extends CActiveRecord{
    
    /**
     * @param type $className
     * @return ExcelPointFormula
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


