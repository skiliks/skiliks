<?php

/**
 * @author slavka
 */
class ZohoDocuments 
{
    protected $apiKey = 'b5e3f7316085c8ece12832f533c751be';
    
    protected $xlsTemplatesDir = null;
    
    protected $templatesDir = null;
    
    protected $saveUrl = 'http://live.skiliks.com/api/index.php/zoho/saveExcel';
    
    protected $docID = null;
    
    protected $simId = null;
    
    protected $responce = null;
    
    protected $templateFilename = null;
    
    protected $extention = null;


    public function __construct($simId, $fileId, $templateFilename, $extention = 'xls')
    {
        $this->xlsTemplatesDir = 'documents/excel';
        $this->templatesDir = 'documents';
        $this->zohoUrl = sprintf(
            'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
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
     * Sends user document to Zoho and store responce
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

        $this->responce = curl_exec($ch);
    }
    
    /**
     * @return string, full URL to Zoho file editor
     */
    public function getUrl()
    {
        $url = null;
        
        $headers = explode("\n", $this->responce);
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
    
    private function getTemplateFilePath()
    {
        $attributeName = $this->extention.'TemplatesDir';
        return sprintf(
            '%s/%s',
            $this->$attributeName,
            $this->templateFilename
        );
    }
    
    private function getUserFilepath()
    {
        return sprintf(
            '%s/%s/%s.xls',
            $this->templatesDir,
            $this->simId,
            $this->docId
         );
    }
    
    private function getDocDirPath()
    {
        return sprintf(
            '%s/%s/', 
            $this->templatesDir,
            $this->simId
        );
    }
    
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

