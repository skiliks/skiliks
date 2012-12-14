<?php



/**
 * Модель  документа Excel
 * 
 * Связана с моделями: ExcelDocumentTemplate, MyDocumentsModel, Simulations.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelDocumentModel extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * excel_document_template.id
     * @var integer
     */
    public $document_id; // ??
    
    /**
     * simulations.id
     * @var int
     */
    public $sim_id;   
            
    /**
     * @var integer
     */
    public $file_id; // ??
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return ExcelDocumentModel 
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
            return 'excel_document';
    }
    
    /**
     * Выбрать для заданной симуляции 
     * @param int $simId
     * @return ExcelDocumentModel 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать для заданного документа
     * @param int $documentId 
     * @return ExcelDocumentModel 
     */
    public function byDocument($documentId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "document_id = {$documentId}"
        ));
        return $this;
    }
    
    /**
     * Выбрать для заданного файла
     * @param int $fileId
     * @return ExcelDocumentModel 
     */
    public function byFile($fileId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "file_id = {$fileId}"
        ));
        return $this;
    }
}


