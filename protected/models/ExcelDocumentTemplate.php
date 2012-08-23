<?php



/**
 * Модель шаблона документа Excel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelDocumentTemplate extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return ExcelDocumentTemplate 
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
            return 'excel_document_template';
    }
    
    /**
     * Вернуть ближайшее событие
     * @return ExcelDocumentTemplate 
     */
    public function byName($name)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "name = '{$name}'"
        ));
        return $this;
    }
    
    public function byFile($fileId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "file_id = {$fileId}"
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
}

?>
