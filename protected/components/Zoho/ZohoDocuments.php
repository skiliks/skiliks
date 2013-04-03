<?php

/**
 * @author slavka
 */
class ZohoDocuments
{
    /**
     * Zoho API key
     *
     * @var string
     */
    protected $apiKey = null;

    /**
     * Path from project root dir.
     *
     * @var string
     */
    protected $xlsTemplatesDirPath = null;

    /**
     * Path from project root dir.
     *
     * @var string
     */
    protected $templatesDirPath = null;

    /**
     * Zoho will send saved document to this URL
     *
     * @var string
     */
    protected $saveUrl = null;

    /**
     * MyDocument.id
     *
     * @var MyDocument
     */
    protected $document = null;

    /**
     * Simulation.id
     *
     * @var integer
     */
    protected $simId = null;

    /**
     * Response from ZohoServer after we upload target document to Zoho server
     *
     * @var string
     */
    public  $response = null;

    /**
     * Filename - used to make user file if it not exists
     * Also used to display for user in Zoho interface.
     *
     * @var string
     */
    protected $templateFilename = null;

    /**
     * File extention, used to detect application.
     * @var string, 'xml', 'doc', 'ptt'
     */
    protected $extention = null;

    protected $srcFilename = null;

    /*
     * @param integer $simId
     * @param integer $fileId
     * @param string $templateFilename
     * @param string $extention
     * @return ZohoDocuments
     */
    public function __construct($simId, $fileId, $templateFilename, $extention = 'xls', $fileName=null)
    {
        $zohoConfigs = Yii::app()->params['zoho'];

        $this->apiKey = $zohoConfigs['apiKey'];
        $this->saveUrl = $zohoConfigs['saveUrl'];
        $this->xlsTemplatesDirPath = $zohoConfigs['xlsTemplatesDirPath']; //'documents/excel';
        $root = __DIR__ . '/../../../';
        $this->templatesDirPath = $root . $zohoConfigs['templatesDirPath']; //'documents';
        $this->zohoUrl = sprintf(
            $zohoConfigs['sendFileUrl'], // 'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor'
            $this->apiKey
        );
        $this->simId = $simId;
        $this->document = MyDocument::model()->findByPk($fileId);
        $this->templateFilename = $templateFilename;
        $this->srcFilename = $fileName;
        $this->extention = $extention;

        /*if (false === $this->checkIsUserFileExists() && $this->templateFilename !== null) {
            $this->copyUserFileIfNotExists();
        }*/
    }

    /**
     * Checks is user copy of system file exists
     *
     * @return boolean
     */
    public function checkIsUserFileExists()
    {
        @$f = fopen($this->getUserFilepath(), 'r'); // w, x, a - creates an empty file.

        if (null === $f || false === $f) {
            return false;
        }

        return true;
    }

    /**
     * Make user copy of system file
     */
    public function copyUserFileIfNotExists()
    {
        // make folder for simulation user files
        if (false === is_dir($this->getDocDirPath())) {
            @mkdir($this->getDocDirPath(), 0777, true);
        }

        assert(is_writable($this->getDocDirPath()));

        $templateFilePath = $this->getTemplateFilePath();
        copy($templateFilePath, $this->getUserFilepath());
    }

    /**
     * Sends user document to Zoho and store response
     */
    public function sendDocumentToZoho()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->zohoUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getExcelFields());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $this->response = curl_exec($ch);
    }

    /**
     * @return string, full URL to Zoho file editor
     */
    public function getUrl()
    {
        $url = null;

        $headers = explode("\n", $this->response);
        foreach ($headers as $value) {
            if (stripos($value, 'Location: ') !== false) {
                $url = str_replace('Location: ', '', $value);
                break;
            }
        }

        return $url;
    }

    /**
     * @param string $returnedId, '1243-1243', $simulation.Id-$myDocuments.Id
     * @param string $tmpFileName, path to temporary OS file, like '/tmp/askd32uds8czjse.xls'
     * @param string $extention, 'xls','doc','ptt'
     *
     * @tutorial: Be careful when update code - this is static method.
     *
     * @return string, status mesage, will be displayed to user
     */
    public static function saveFile($returnedId, $tmpFileName, $extention)
    {
        $path = explode('-', $returnedId);

        if (2 !== count($path)) {
            return 'RESPONSE: Wrong document id!';
        }

        $uuid = MyDocument::model()->findByPk($path[1])->uuid;

        $pathToUserFile = sprintf(
            'documents/zoho/%s.%s',
            $uuid,
            $extention
        );

        move_uploaded_file($tmpFileName, $pathToUserFile);

        return 'Saved.';
    }

    // --- Private methods ---------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    private function getTemplateFilePath()
    {
        $attributeName = $this->extention . 'TemplatesDirPath';
        $path = sprintf(
            __DIR__ . '/../../../%s/%s',
            $this->$attributeName,
            $this->templateFilename
        );
        return $path;
    }

    /**
     * @return string
     */
    public function getUserFilepath()
    {
        return sprintf(
            '%s/%s/%s.xls',
            $this->templatesDirPath,
            $this->simId,
            $this->document->uuid
        );
    }

    /**
     * @return string
     */
    private function getDocDirPath()
    {
        return sprintf(
            '%s/%s/',
            $this->templatesDirPath,
            $this->simId
        );
    }

    /**
     * @return string
     */
    private function getExcelFields()
    {
        return array(
            'content' => '@' . $this->getUserFilepath(),
            'filename' => (null === $this->srcFilename) ? $this->templateFilename : $this->srcFilename,
            'id' => $this->simId . '-' . $this->document->getPrimaryKey(),
            'format' => 'xls',
            'saveurl' => $this->saveUrl,
            'mode' => 'normaledit'
        );
    }
    /*
     * Копирование всех файлов для захо при старте симуляции
     */
    public static function copyExcelFiles($simId) {

        $zohoConfigs = Yii::app()->params['zoho'];
        // нужно на ORM
        $documents = MyDocument::model()->with('template')->findAllByAttributes(['sim_id' => $simId]);
        $path_zoho = __DIR__ . '/../../../'.$zohoConfigs['templatesDirPath'].'/';

        foreach($documents as $document){
            $xls = __DIR__ . '/../../../'.$zohoConfigs['xlsTemplatesDirPath'].'/'.$document->template->srcFile;
            if(file_exists($xls)){
                copy($xls, $path_zoho.'/'.$document->uuid.'.'.$zohoConfigs['extExcel']);
            }

        }


    }
}

