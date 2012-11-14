<?php



/**
 * Модель рабочих листов шаблона excel документа
 * В данном случаи это шаблон, который потом копируется в рамках
 * конкретной симуляции.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelWorksheetTemplate extends CActiveRecord{
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'excel_worksheet_template';
    }
    
    /**
     * Вернуть ближайшее событие
     * @return ExcelDocumentTemplate 
     */
    public function byDocument($documentId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "document_id = {$documentId}"
        ));
        return $this;
    }
}

?>
