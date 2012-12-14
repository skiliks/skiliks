<?php



/**
 * Шаблон соства конкретного рабочего листа документа. Содержит набор
 * ячеек, образующих лист а также их свойства.
 *
 * Связана с моделями: ExcelWorksheetTemplate
 * 
 * @deprecated 
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelWorksheetTemplateCells extends CActiveRecord{
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'excel_worksheet_template_cells';
    }
    
    /**
     * Выбрать по заданному рабочему листу
     * @param int $worksheetId
     * @return ExcelWorksheetTemplateCells 
     */
    public function byWorksheet($worksheetId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "worksheet_id = {$worksheetId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по набору рабочих листов
     * @param array $worksheets  набор идентификаторов рабочих листов
     * @return ExcelWorksheetTemplateCells 
     */
    public function byWorksheets($worksheets)
    {
        $worksheets = implode(',', $worksheets);
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "worksheet_id in ({$worksheets})"
        ));
        return $this;
    }
}


