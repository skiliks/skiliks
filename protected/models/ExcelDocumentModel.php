<?php



/**
 * Модель  документа Excel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelDocumentModel extends CActiveRecord{
    
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
     * Вернуть ближайшее событие
     * @return ExcelDocumentTemplate 
     */
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId}"
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
}

?>
