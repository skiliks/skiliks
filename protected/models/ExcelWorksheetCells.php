<?php



/**
 * Содержит состав рабочего листа Excel в рамках конкретной симуляции.
 *
 * Связана с моделями:  ExcelWorksheetModel.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelWorksheetCells extends CActiveRecord{
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'excel_worksheet_cells';
    }
    
    /**
     * Вернуть по заданному рабочему листу
     * @param int $worksheetId
     * @return ExcelDocumentTemplate 
     */
    public function byWorksheet($worksheetId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "worksheet_id = {$worksheetId}"
        ));
        return $this;
    }
}

?>
