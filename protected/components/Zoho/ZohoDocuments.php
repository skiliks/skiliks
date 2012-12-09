<?php

/**
 * @author slavka
 */
class ZohoDocuments 
{
    protected $apiKey = 'b5e3f7316085c8ece12832f533c751be';
    
    protected $xlsTemplatesDir = null;
    
    protected $format = 'xls';
    
    protected $saveUrl = 'http://live.skiliks.com/api/index.php/zoho/saveExcel'; // http://backend.live.skiliks.com/tmp/zoho_save.php
    
    protected $docID = null;
    
    protected $zohoUrl;
    
    protected $simId = null;
    
    protected $responce = null;


    public function __construct($simId = null)
    {
        $this->xlsTemplatesDir = 'documents/excel';
        $this->zohoUrl = sprintf(
            'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
            $this->apiKey
        );
        $this->simId = $simId;
    }
    
    public function copyUserFileIfNotExists($templateFilename, $fileId)
    {
        $defauleFileTemplatePath = sprintf(
            '%s%s/%s',
            '',
            $this->xlsTemplatesDir,
            $templateFilename
        );
        
        $pathToCustomUserFile = sprintf(
            '%s%s/%s/%s/%s',
            '',
            $this->xlsTemplatesDir,
            $this->simId,
            $fileId,
            StringTools::CyToEn($templateFilename)
        );
        
        @$f = fopen($pathToCustomUserFile, 'r'); // x+ only return false
        
        if(null === $f || false === $f){

            @mkdir(sprintf(
                '%s%s/%s/',
                '', // /var/www/skiliks_git/backend
                $this->xlsTemplatesDir,
                $this->simId
            ));
            @mkdir(sprintf(
                '%s%s/%s/%s/',
                '', // /var/www/skiliks_git/backend
                $this->xlsTemplatesDir,
                $this->simId,
                $fileId
            ));
            
            copy($defauleFileTemplatePath, $pathToCustomUserFile);
        }
    }


    public function getExcelFields($xlsTemplateFilename, $docId)
    {
        // @todo: move to constructor
        $this->docId = $docId;
        
        return array(
            'content'  => sprintf(
                '@%s/%s/%s/%s',
                $this->xlsTemplatesDir,
                $this->simId,
                $docId,
                StringTools::CyToEn($xlsTemplateFilename)
             ),
            'filename' => sprintf(
                '%s-%s-%s',                
                $this->simId,
                $docId,
                StringTools::CyToEn($xlsTemplateFilename)
            ),
            'id'       => $this->docId,
            'format'   => $this->format,
            'saveurl'  => $this->saveUrl.'?id='.$this->simId.'-'.$this->docId,
        );
    }
    
    public function openExcelDocument($xlsTemplateFilename, $docId)
    {
        $url = null;
        
        // var_dump($this->getExcelFields($xlsTemplateFilename, $docId));
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->zohoUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getExcelFields($xlsTemplateFilename, $docId));
        curl_setopt($ch, CURLOPT_VERBOSE,  1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $this->responce = curl_exec($ch);
        
        $headers = explode("\n", $this->responce);
        foreach($headers as $value)
        {
            if (stripos($value, 'Location: ') !== false)
            {
                $url = str_replace('Location: ', '', $value);
            }
        }

        return array(
            'url' => $url
        );
    }
}

