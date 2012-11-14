<?php



/**
 * Модель рабочих листов шаблона excel документа
 * Содержит листы в рамках документа Excel.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelWorksheetModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return ExcelWorksheetModel 
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
            return 'excel_worksheet';
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
    
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    public function byName($name)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "name = '{$name}'"
        ));
        return $this;
    }
}

?>
