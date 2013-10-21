<?php
/**
 * Шаблон документов. Потом нужные документы отсюда копируются в рамках симуляции 
 * в таблицу my_documents
 */
class DocumentTemplate extends CActiveRecord implements IGameAction
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

    protected static $mimeMap = [
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint',
        'xls' => 'application/vnd.ms-excel'
    ];
    
    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     * @return string
     */
    public function getCacheFilePath()
    {
        $filename = substr($this->fileName, 0, strrpos($this->fileName, '.'));
        $filename = str_replace(' ', '_', $filename);

        return __DIR__ . '/../../../documents/socialcalc_templates/' .
            StringTools::CyToEn( $filename. '.sc');
    }

    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     *
     * @param type $className
     * @return DocumentTemplate
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
     * @return DocumentTemplate
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
     * @return DocumentTemplate
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }


    public function getCode()
    {
        return $this->code;
    }

    /**
     * Выбрать по заданному набору шаблонов документов
     * @param array $ids
     * @return DocumentTemplate
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


