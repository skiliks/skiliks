<?php
/**
 * Класс для работы с Excel документом
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
final class ExcelDocument 
{
        /**
     * Excel file in .xls format. We will send it to Zoho.
     * @var string
     */
    private $file = null;
    
    protected $zohoDocument = null;
    
    public function loadByFile($simId, $fileId) {
        // проверить есть ли такой файл
        $file = MyDocumentsService::existsInSim($simId, $fileId);
        if (null === $file) { 
            throw new Exception("Для вашей симуляции отсутствует файл");
        }
        // получим документ из шаблонов
        $documentTemplate = ExcelDocumentTemplate::model()->byFile($file->template_id)->find();
        if (null === $documentTemplate) {
            throw new Exception("Немогу загрузить шаблон документа для $file->template_id");
        }
        
        $this->file = $documentTemplate;
        
        $this->zohoDocument[$simId][$fileId] = new ZohoDocuments($simId, $fileId, $this->file->getRealFileName());
        $this->zohoDocument[$simId][$fileId]->sendDocumentToZoho();
        
        return $this;
    }
    
    /**
     * Add url for Zoho-docunebt iframe
     * 
     * @return array of strings
     */
    public function populateFrontendResult($simId, $fileId) 
    {
        if (false === isset($this->zohoDocument[$simId][$fileId])) {
            $this->zohoDocument[$simId][$fileId] = new ZohoDocuments($simId, $fileId, $this->file->getRealFileName());
            $this->zohoDocument[$simId][$fileId]->sendDocumentToZoho();
        }        
        
         return array(
            'result' => 1,
             'excelDocumentUrl' => $this->zohoDocument[$simId][$fileId]->getUrl(),
        );
    }
}


