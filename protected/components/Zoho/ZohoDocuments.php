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
     * @var integer
     */
    protected $docID = null;
    
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
    protected $response = null;
    
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

    /*
     * @param integer $simId
     * @param integer $fileId
     * @param string $templateFilename
     * @param string $extention
     */
    public function __construct($simId, $fileId, $templateFilename, $extention = 'xls')
    {
        $zohoConfigs = Yii::app()->params['zoho'];
        
        $this->apiKey = $zohoConfigs['apiKey'];
        $this->saveUrl = $zohoConfigs['saveUrl'];
        $this->xlsTemplatesDirPath = $zohoConfigs['xlsTemplatesDirPath']; //'documents/excel';
        $this->templatesDirPath = $zohoConfigs['templatesDirPath']; //'documents';
        $this->zohoUrl = sprintf(
            $zohoConfigs['sendFileUrl'], // 'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor'
            $this->apiKey
        );
        $this->simId = $simId;
        $this->docId = $fileId;
        $this->templateFilename = $templateFilename;
        $this->extention = $extention;
        
        if (false === $this->checkIsUserFileExists()) {
            $this->copyUserFileIfNotExists();
        }
    }
    
    /**
     * Checks is user copy of system file exists
     * 
     * @return boolean
     */
    public function checkIsUserFileExists() 
    {
        @$f = fopen($this->getUserFilepath(), 'r'); // w, x, a - creates an empty file.
        
        if(null === $f || false === $f) {
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
            @mkdir($this->getDocDirPath());
        }

        copy($this->getTemplateFilePath(), $this->getUserFilepath());
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
        curl_setopt($ch, CURLOPT_VERBOSE,  1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $this->response = curl_exec($ch);
        Yii::log($this->response);
    }
    
    /**
     * @return string, full URL to Zoho file editor
     */
    public function getUrl()
    {
        $url = null;
        
        $headers = explode("\n", $this->response);
        foreach($headers as $value)
        {
            if (stripos($value, 'Location: ') !== false)
            {
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
        
        $pathToUserFile = sprintf(
            'documents/%s/%s.%s',
            $path[0], // simId
            $path[1], // documentID,
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
        $attributeName = $this->extention.'TemplatesDirPath';
        return sprintf(
            '%s/%s',
            $this->$attributeName,
            $this->templateFilename
        );
    }
    
    /**
     * @return string
     */
    private function getUserFilepath()
    {
        return sprintf(
            '%s/%s/%s.xls',
            $this->templatesDirPath,
            $this->simId,
            $this->docId
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
            'content'  => '@'.$this->getUserFilepath(),
            'filename' => $this->templateFilename,
            'id'       => $this->simId.'-'.$this->docId,
            'format'   => 'xls',
            'saveurl'  => $this->saveUrl,
        );
    }
}

