<?php
/**
 * Класс для работы с Excel документом
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
final class ExcelDocument {
    
    private $_id;
    
    private $_activeWorksheet = 0;
    
    private $_defaultWorksheetId = false;
    
    /**
     * Excel file in .xls format. We will send it to Zoho.
     * @var string
     */
    private $file = null;
    
    /**
     *
     * @var array
     */
    protected $_worksheets = array();
    
    /**
     * Хранит соответствие $worksheetName => $worksheetId
     * @var array
     */
    protected $_worksheetMap = array();
    
    protected $zohoDocument = null;


    function __construct($documentId = false) {
        $sid = SessionHelper::getSid();
        $simId = SessionHelper::getSimIdBySid($sid);
        
        if ($documentId) {
            
            $this->_loadWorksheets($documentId); // загрузим рабочие листы
        }
    }
    
    /**
     *
     * @return ExcelWorksheet
     */
    public function getActiveWorksheet() {
        return $this->getWorksheet($this->_activeWorksheet);
    }
    
    /**
     *
     * @param int $worksheetId
     * @return ExcelWorksheet 
     */
    public function getWorksheet($worksheetId) {
        if (isset($this->_worksheets[$worksheetId])) {
            return $this->_worksheets[$worksheetId];
        }
        
        // загружаем рабочий лист
        $worksheet = new ExcelWorksheet();
        $worksheet->load($worksheetId);
        
        $this->_setWorksheet($worksheetId, $worksheet);
        return $this->_worksheets[$worksheetId];
    }
    
    protected function _setWorksheet($worksheetId, ExcelWorksheet $worksheet) {
        $this->_worksheets[$worksheetId] = $worksheet;
    }
    
    public function getWorksheetByName($name) {
        if (!isset($this->_worksheetMap[$name])) return false;
   
        $worksheetId = $this->_worksheetMap[$name];
        
        return $this->getWorksheet($worksheetId);
    }
    
    public function activateWorksheetByName($name) {
        $worksheetId = (int)$this->getWorksheetIdByName($name);
        if ($worksheetId == 0) throw new Exception("cant get worksheet by name : $name");
        return $this->loadWorksheet($worksheetId);
    }
    
    public function getWorksheetIdByName($name) {
        if (isset($this->_worksheetMap[$name])) return $this->_worksheetMap[$name];
        return false;
    }
    
    public function loadWorksheet($worksheetId) {
        $this->getWorksheet($worksheetId);
        $this->_activeWorksheet = $worksheetId;
        return $this;
    }
    
    /**
     * Загружает соответствие рабочих листов
     * @param type $documentId 
     */
    protected function _loadWorksheets($documentId) 
    {
        $worksheets = ExcelWorksheetModel::model()->byDocument($documentId)->findAll();

        foreach($worksheets as $worksheet) {
            if (!$this->_defaultWorksheetId) $this->_defaultWorksheetId = $worksheet->id;
            $this->_worksheetMap[$worksheet->name] = $worksheet->id;
        }
    }
    
    // deprecated!
    public function loadDefault($simId) {
        $document = ExcelDocumentModel::model()->bySimulation($simId)->find();
        if (!$document) throw new Exception('cant find document');
        
        $worksheetId = $this->_defaultWorksheetId;
        
        // загружаем рабочий лист
        $this->_activeWorksheet = $worksheetId;

        $profiler = new Profiler();
        $profiler->startTimer();
        
        $this->_setWorksheet($worksheetId, $this->getWorksheet($worksheetId));
        $t = $profiler->endTimer();
        //Logger::debug("_loadWorksheetIfNeeded : $t");
        
        return $this;
    }
    
    public function loadByFile($simId, $fileId) {
        // проверить есть ли такой файл
        $file = MyDocumentsService::existsInSim($simId, $fileId);
        if (null === $file) { 
            throw new Exception("Для вашей симуляции отсутствует файл");
        }
        // получим документ из шаблонов
        $documentTemplate = ExcelDocumentTemplate::model()->byFile($file->template_id)->find();
        if (null === $documentTemplate) {
            throw new Exception("Немогу загрузить шаблон докмента для $templateId");
        }
        
        $this->file = $documentTemplate;
        
        $this->zohoDocument[$simId][$fileId] = new ZohoDocuments($simId, $fileId, $this->file->getRealFileName());
        $this->zohoDocument[$simId][$fileId]->sendDocumentToZoho();
        
        /*
        // проверить есть ли у нас такой документ
        $document = ExcelDocumentModel::model()->bySimulation($simId)->byFile($fileId)->find();
        if (!$document) {
            // пока документа нет, значит надо его залить в симуляцию
        );
            
            // скопируем пользователю документ
            $documentId = ExcelDocumentService::copy($documentTemplate->id, $simId);
            if (!$documentId) throw new Exception("Неудалось скопировать документ");
        }
        else {
            $documentId = $document->id;
        }
        
        // получить первый  воркшит
        $this->_loadWorksheets($documentId);
        $this->_activeWorksheet = $this->_defaultWorksheetId;
        
        // загрузить worksheet
        $this->_setWorksheet($this->_defaultWorksheetId, $this->getWorksheet($this->_defaultWorksheetId));
        */
        
        return $this;
    }
    
    /**
     * Add url for Zoho-docunebt iframe
     * 
     * @return array of strings
     */
    public function populateFrontendResult($simId, $fileId) 
    {
        /*$zohoDocuments = new ZohoDocuments($simId);
        $zohoResults = $zohoDocuments->openExcelDocument(
            $this->file->getRealFileName(),
            $fileId
        );*/
        
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


