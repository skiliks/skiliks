<?php



/**
 * Модель шаблона документа Excel
 * 
 * Связана с моделями:  MyDocumentsTemplateModel.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelDocumentTemplate extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * @var string
     */
    public $name;
    
    /**
     * @var integer
     */
    public $file_id; // ??
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
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
     * Вернуть документ по имени
     * @param string $name
     * @return ExcelDocumentTemplate 
     */
    public function byName($name)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "name = '{$name}'"
        ));
        return $this;
    }
    
    /**
     * Выбрать по идентификатору файла
     * @param int $fileId
     * @return ExcelDocumentTemplate 
     */
    public function byFile($fileId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "file_id = {$fileId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по идентификатору
     * @param int $id
     * @return ExcelDocumentTemplate 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    /**
     * Files placed at {root}/documents/excel/
     * @return string
     */
    public function getRealFileName() {
        return $this->name;
    }

}
