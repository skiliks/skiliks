<?php
/**
 * Шаблон документов. Потом нужные документы отсюда копируются в рамках симуляции 
 * в таблицу my_documents
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MyDocumentsTemplateModel extends CActiveRecord
{
    const CONSOLIDATED_BUDGET_ID = 33;
    
    /**
     * @var integer
     */
    public $id;
    
    /**
     * @var string
     */
    public $fileName;
    
    /**
     * is hidden
     * @var integer, (boolean)
     */
    public $hidden;
    
    /**
     * Code, '','' ...
     * @var string
     */
    public $code;
    
    /**
     * @var string
     */
    public $srcFile;
    
    /**
     * 'xml', 'doc', 'ptt'
     * @var string
     */
    public $format;
    
    /**
     * '-', 'new', 'start'
     * @var string
     */
    public $type;
    
    /**
     * @var string
     */
    public $import_id;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return MyDocumentsTemplateModel 
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
            return 'my_documents_template';
    }
    
    /**
     * Выбрать документ по коду
     * @param string $code
     * @return MyDocumentsTemplateModel 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
    
    /**
     * Выбрать заданный документ
     * @param int $id
     * @return MyDocumentsTemplateModel 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }

    public function getMimeType() {
        
        // tweak for not ready files, in ready project we willn`t need it any more
        if (in_array($this->srcFile, ['TP', 'MG'])) {
            return 'plain/text';
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $this->getFilePath());
        finfo_close($finfo);
        return $mime;
    }

    private function getFilePath()
    {
        $zohoConfigs = Yii::app()->params['zoho'];

        $path = sprintf("%s/../../%s/%s",
            __DIR__,
            $zohoConfigs['xlsTemplatesDirPath'],
            $this->srcFile
        );

        return $path;
    }
    
    /**
     * Выбрать по заданному набору шаблонов документов
     * @param array $ids
     * @return MyDocumentsTemplateModel 
     */
    public function byIds($ids)
    {
        if (count($ids) == 0) return $this;
        $ids = implode(',', $ids);
        
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$ids})"
        ));
        return $this;
    }
}


