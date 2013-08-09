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
        $filePath = $this->getFilePath();
        $cachePath = $this->getCacheFilePath();

        if ($filename && is_file($filename)) {
            $scData = unserialize(file_get_contents($filename));
        } elseif (is_file($filePath) || is_file($cachePath)) {
            $scData = unserialize(file_get_contents(is_file($filePath) ? $filePath : $cachePath));
        } else {
            $scData = ScXlsConverter::xls2sc($this->template->getFilePath());
        }

        if (null === $filename) {
            if (!is_file($filePath)) {
                file_put_contents($filePath, serialize($scData));
            }
            if (!is_file($cachePath)) {
                file_put_contents($cachePath, serialize($scData));
            }
        }

        return array_values($scData);
    }

    public function setSheetContent($name, $sheetContent, $filename = null)
    {
        $filePath = $filename ?: $this->getFilePath();
        $scData = file_exists($filePath) ? unserialize(file_get_contents($filePath)) : [];

        $scData[$name] = [
            'name' => $name,
            'content' => $sheetContent
        ];

        file_put_contents($filePath, serialize($scData));
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        $filename = substr($this->fileName, 0, strrpos($this->fileName, '.'));
        $filename = str_replace(' ', '_', $filename);
        return __DIR__ . '/../../../documents/user/' . $this->sim_id . '_' . StringTools::CyToEn($filename);
    }

    /**
     * @return string
     */
    public function getCacheFilePath()
    {
        return $this->template->getCacheFilePath();
    }

    public function backupFile($extension = 'broken')
    {
        $filepath = $this->getFilePath() . '.' . $extension;
        if (is_file($this->getFilePath())) {
            copy($this->getFilePath(), $filepath);
            return true;
        }

        return false;
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


