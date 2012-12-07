<?php

/**
 * @author slavka
 */
class ZohoDocuments 
{
    protected $apiKey = 'b5e3f7316085c8ece12832f533c751be';
    
    protected $xlsTemplatesDir = null;
    
    protected $format = 'xls';
    
    protected $saveUrl = 'http://backend.live.skiliks.com/tmp/zoho_save.php';
    
    protected $id = 13;
    
    protected $zohoUrl;
    
    public function __construct()
    {
        $this->xlsTemplatesDir = 'documents/excel';
        $this->zohoUrl = sprintf(
            'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
            $this->apiKey
        );
    }
    
    public function getExcelFields($xlsTemplateFilename)
    {
        return array(
            'content'  => sprintf(
                '@%s/%s',
                $this->xlsTemplatesDir,
                $xlsTemplateFilename
             ),
            'filename' => $xlsTemplateFilename,
            'id'       => $this->id,
            'format'   => $this->format,
            'saveurl'  => $this->saveUrl
        );
    }
    
    public function openExcelDocument($xlsTemplateFilename)
    {
        $url = null;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->zohoUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getExcelFields($xlsTemplateFilename));
        curl_setopt($ch, CURLOPT_VERBOSE,  1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $content = curl_exec ($ch);
        
        $headers = explode("\n", $content);
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

