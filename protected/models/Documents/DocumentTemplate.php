<?php
/**
 * Шаблон документов. Потом нужные документы отсюда копируются в рамках симуляции 
 * в таблицу my_documents
 *
 * @property integer $id
 * @property integer $hidden (boolean), is hidden
 * @property string  $fileName
 * @property string  $code
 * @property string  $srcFile
 * @property string  $format, 'xml', 'doc', 'ptt'
 * @property string  $type, '-', 'new', 'start'
 * @property string  $import_id
 */
class DocumentTemplate extends CActiveRecord implements IGameAction
{
    const CONSOLIDATED_BUDGET_ID = 33;

    protected static $mimeMap = [
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint',
        'xls' => 'application/vnd.ms-excel'
    ];
    
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

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed|string
     */
    public function getMimeType() {
        // tweak for not ready files, in ready project we willn`t need it any more
        if (in_array($this->srcFile, ['TP', 'MG'])) {
            return 'plain/text';
        }

        if (isset(self::$mimeMap[$this->format])) {
            return self::$mimeMap[$this->format];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $this->getFilePath());
        finfo_close($finfo);
        return $mime;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->getPathFromName($this->srcFile);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPages() {
        if($this->format === 'docx' || $this->format === 'pptx') {
            $pdf_dir = str_replace('.pdf', '', $this->srcFile);
            if(is_dir($this->getPathFromName($pdf_dir))) {
                $pages = [];
                foreach(scandir($this->getPathFromName($pdf_dir)) as $filename){
                    if($filename !== "." && $filename !== ".."){
                        $pages[] = $pdf_dir.'/'.$filename;
                    }
                }
                return $pages;
            } else {
                throw new Exception('Dir '.$this->getPathFromName($pdf_dir).' not found');
            }
        }else{
            return [];
        }
    }

    /**
     * @param $name
     * @return string
     */
    public function getPathFromName($name)
    {
        if (-1 < (strstr($name, '.xls'))) {
            return __DIR__."/../../../documents/templates/".$name;
        }

        // JPGs: doc, ptt
        return __DIR__."/../../../protected/assets/img/documents/templates/".$name;
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
}


