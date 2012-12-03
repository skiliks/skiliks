<?php



/**
 * Модель рабочих листов шаблона excel документа
 * Содержит листы в рамках документа Excel.
 * 
 * Связана с моделями:  ExcelDocument.
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
     * Выбрать по заданному документу
     * @param int $documentId
     * @return ExcelDocumentTemplate 
     */
    public function byDocument($documentId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "document_id = {$documentId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по иднетификатору
     * @param int $id
     * @return ExcelWorksheetModel 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по имени рабочего листа
     * @param string $name
     * @return ExcelWorksheetModel 
     */
    public function byName($name)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "name = '{$name}'"
        ));
        return $this;
    }
}


