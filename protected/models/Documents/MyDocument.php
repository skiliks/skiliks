<?php
/**
 * Модель моих документов
 *
 * @property integer $id
 * @property integer $is_was_saved, 1 or 0
 * @property integer $sim_id
 * @property integer $template_id
 * @property integer $hidden
 * @property string  $uuid
 * @property string  $fileName
 *
 * @property DocumentTemplate template
 */
class MyDocument extends CActiveRecord
{
    /**
     * @param string $filename
     *
     * @return array os string
     */
    public function getSheetList($filename = null)
    {
        $filePath = $this->getFilePath();
        $cachePath = $this->getCacheFilePath();

        if ($filename && is_file($filename)) {
            $scData = json_decode(file_get_contents($filename), true);

        } elseif (is_file($filePath) || is_file($cachePath)) {
            $scData = json_decode(file_get_contents(is_file($filePath) ? $filePath : $cachePath), true);
        } else {
            $scData = ScXlsConverter::xls2sc($this->template->getFilePath());
        }

        if (null === $scData) {
            $scData = [];
        }

        if (null === $filename) {
            if (!is_file($filePath)) {
                file_put_contents($filePath, json_encode($scData));
            }
            if (!is_file($cachePath)) {
                file_put_contents($cachePath, json_encode($scData));
            }
        }

        return array_values($scData);
    }

    /**
     * @param $name
     * @param $sheetContent
     * @param null $filename
     */
    public function setSheetContent($name, $sheetContent, $filename = null)
    {
        $filePath = $filename ?: $this->getFilePath();
        $scData = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];

        $scData[$name] = [
            'name' => $name,
            'content' => $sheetContent
        ];

        file_put_contents($filePath, json_encode($scData));
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

    /**
     * @param string $extension
     * @return bool
     */
    public function backupFile($extension = 'broken')
    {
        $filePath = $this->getFilePath() . '.' . $extension;
        if (is_file($this->getFilePath())) {
            copy($this->getFilePath(), $filePath);
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

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     * @param string $className
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

    public function relations()
    {
        return [
            'template' => [self::BELONGS_TO, 'DocumentTemplate', 'template_id'],
            'simulation' => [self::BELONGS_TO, 'Simulation', 'sim_id'],
        ];
    }
}


