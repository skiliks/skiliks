<?php



/**
 * Шаблон соответствий имен файлов для конкретного файл в системе.
 *
 * Связана с моделями: MyDocumentsTemplateModel.
 * 
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ViewerTemplateModel extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * my_documents_tamplate.id
     * @var integer
     */
    public $file_id;
    
    /**
     * @var string
     */
    public $filePath;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return ViewerTemplateModel
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
            return 'viewer_template';
    }
    
    /**
     * Выбрать по заданному файлу
     * @param int $fileId
     * @return ViewerTemplateModel 
     */
    public function byFile($fileId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "file_id = {$fileId}"
        ));
        return $this;
    }
}


