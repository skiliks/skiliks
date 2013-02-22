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

    public function loadByFile($simId, $file) {
        // получим документ из шаблонов
        $documentTemplate = ExcelDocumentTemplate::model()->byFile($file->template_id)->find();
        if (null === $documentTemplate) {
            throw new Exception("Can not load document for $file->template_id");
        }
        
        $this->file = $documentTemplate;
        
        $this->zohoDocument[$simId][$file->id] = new ZohoDocuments($simId, $file->id, $this->file->getRealFileName());
        $this->zohoDocument[$simId][$file->id]->sendDocumentToZoho();
        
        return $this;
    }
    
    /**
     * Add url for Zoho-docunebt iframe
     * 
     * @param Simulations $simulation
     * @param integer $fileId
     * 
     * @return array of strings
     */
    public function populateFrontendResult($simulation, $file)
    {
        if (NULL === $file) {
            return array(
                'result'           => 0,
                'filedId'          => $file->id,
                'excelDocumentUrl' => '/pages/excel/fileNotFound.html',
            );
        }
        
        if (false === isset($this->zohoDocument[$simulation->id][$file->id])) {
            $this->zohoDocument[$simulation->id][$file->id] =
                new ZohoDocuments($simulation->id, $file->id, $this->file->getRealFileName());
            
            $this->zohoDocument[$simulation->id][$file->id]->sendDocumentToZoho();
        }        
        
         return array(
            'result'           => 1,
            'filedId'          => $file->id,
            'excelDocumentUrl' => $this->zohoDocument[$simulation->id][$file->id]->getUrl(),
        );
    }
}


