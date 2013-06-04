<?php


/**
 * Модель моих документов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 *
 * @property DocumentTemplate template
 * @property string $uuid
 * @property int $is_was_saved
 */
class MyDocument extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * simulations.id
     * @var int
     */
    public $sim_id;
    
    /**
     * my_documents_template.id
     * @var integer
     */
    public $template_id;
    
    /**
     * @var string
     */
    public $fileName;
    
    /**
     * is hidden
     * @var integer, (boolean)
     */
    public $hidden;

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return MyDocument 
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
            return 'my_documents';
    }

    /**
     * Выбрать заданный документ
     * @param int $id
     * @return MyDocument 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному имени файла
     * @param string $fileName
     * @return MyDocument 
     */
    public function byFileName($fileName)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "fileName = '{$fileName}'"
        ));
        return $this;
    }
    
    /**
     * Отсортировать по имени файла
     * @return MyDocument 
     */
    public function orderByFileName()
    {
        $this->getDbCriteria()->mergeWith(array(
            'order' => "fileName asc"
        ));
        return $this;
    }
    
    /**
     * Выбрать по заданному шаблону документа
     * @param int $templateId
     * @return MyDocument 
     */
    public function byTemplateId($templateId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "template_id = {$templateId}"
        ));
        return $this;
    }

    /**
     * Выбрать только видимые документы 
     * @return MyDocument 
     */
    public function visible()
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "hidden = 0"
        ));
        return $this;
    }

    public function relations()
    {
        return [
            'template' => [self::BELONGS_TO, 'DocumentTemplate', 'template_id'],
            'simulation' => [self::BELONGS_TO, 'Simulation', 'sim_id'],
        ];
    }

    /**
     * Returns sheet list
     * @return array
     */
    public function getSheetList($filename = null)
    {
        $filePath = $filename ?: (file_exists($this->getFilePath()) ? $this->getFilePath() : $this->template->getFilePath());
        $fileData = yaml_parse_file($filePath);
        return $fileData;
    }

    public function setSheetContent($name, $sheetContent, $filename = null)
    {
        $filePath = $filename ?: (file_exists($this->getFilePath()) ? $this->getFilePath() : $this->template->getFilePath());
        $fHandle = fopen($filePath, 'r');
        try {
            flock($fHandle, LOCK_EX);
            $content = yaml_parse_file($filePath);
            foreach ($content as &$sheet) {
                if ($sheet['name'] === $name) {
                    $sheet['content'] = $sheetContent;
                }
            }
            $yamlContent = yaml_emit($content);
            $filePath = $this->getFilePath();
            $result = file_put_contents($filePath, $yamlContent);
            fclose($fHandle);
        } catch (Exception $e) {
            fclose($fHandle);
            throw $e;
        }
        if ($result === false) {
            assert('Can not save sheet');
        }
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return __DIR__ . '/../../../documents/user/' . $this->uuid . '.sc';
    }

    /**
     * Creates UUID to every document
     * @return bool
     */
    protected function beforeSave()
    {
        if (!$this->uuid) {
            $this->uuid = new CDbExpression('UUID()');
        }
        return parent::beforeSave();
    }
}


