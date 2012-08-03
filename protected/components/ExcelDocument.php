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
     *
     * @var array
     */
    protected $_worksheets = array();
    
    /**
     * Хранит соответствие $worksheetName => $worksheetId
     * @var array
     */
    protected $_worksheetMap = array();
    
    function __construct() {
        $sid = SessionHelper::getSid();
        $simId = SessionHelper::getSimIdBySid($sid);
        
        $document = ExcelDocumentModel::model()->bySimulation($simId)->find();
        if (!$document) throw new Exception('cant find document');
        $this->_id = $document->id;

        Logger::debug("ExcelDocument construct");
        // загрузим рабочие листы
        $this->_loadWorksheets($document->id);
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
            
        return $this->getWorksheet($this->_worksheetMap[$name]);
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
    protected function _loadWorksheets($documentId) {
        Logger::debug("_loadWorksheets $documentId");
        $worksheets = ExcelWorksheetModel::model()->byDocument($documentId)->findAll();
        //Logger::debug('list : '.var_export($worksheets));
        foreach($worksheets as $worksheet) {
            if (!$this->_defaultWorksheetId) $this->_defaultWorksheetId = $worksheet->id;
            $this->_worksheetMap[$worksheet->name] = $worksheet->id;
        }
        Logger::debug('loaded _worksheetMap : '.var_export($this->_worksheetMap, true));
    }
    
    public function loadDefault($simId) {
        $document = ExcelDocumentModel::model()->bySimulation($simId)->find();
        if (!$document) throw new Exception('cant find document');

        
        
        
        $worksheetId = $this->_defaultWorksheetId;
        

        Logger::debug('$worksheetId : '.var_export($worksheetId, true));
        
        // загружаем рабочий лист
        $this->_activeWorksheet = $worksheetId;

        $profiler = new Profiler();
        $profiler->startTimer();
        
        $this->_setWorksheet($worksheetId, $this->getWorksheet($worksheetId));
        
        return $this;
        
        $t = $profiler->endTimer();
        Logger::debug("_loadWorksheetIfNeeded : $t");

        $profiler->startTimer();
        $frontendData = $this->_populateFrontendResult($worksheet);
        $t = $profiler->endTimer();
        Logger::debug("_populateFrontendResult : $t");
        
        $result['worksheetData'] = $frontendData['worksheetData'];
        $result['strings'] = $frontendData['strings'];
        $result['columns'] = $frontendData['columns'];
        return $result;
    }
    
    public function populateFrontendResult($worksheet=false) {
        
        if (!$worksheet) $worksheet = $this->getWorksheet ($this->_activeWorksheet);
        
        $list = array();
        foreach($this->_worksheetMap as $worksheetName=>$worksheetId) {
            $list[] = array(
                'id' => $worksheetId,
                'title' => $worksheetName
            );
        }
        
        
        $result = array();
        $result['result'] = 1;
        $result['worksheets'] = $list;
        $result['currentWorksheet'] = $worksheet->id;
        
        $worksheetData = array();
        $cells = $worksheet->getCells();
        //Logger::debug('cells : '.var_export($cells, true));
        
        $excelFormula = new ExcelFormula();
        foreach($cells as $column=>$strings) {
            Logger::debug("column : $column");
            foreach($strings as $string=>$cell) {
                Logger::debug("string : $string");
                // обрабатываем формулы
                if ($cell['formula'] != '') {
                    $value = $excelFormula->parse($cell['formula']);
                    if ($value) {
                        $cell['value'] = $value;
                    }    
                }

                if ($excelFormula->hasLinkVar($cell['formula'])) {
                    $cell['read_only'] = 1;
                }
                // постобработка
                //$cell['value'] = $this->_processValue($cell['value']);

                $worksheetData[] = $cell;
            }
        }
        $result['worksheetData'] = $worksheetData;
        $result['strings'] = count($cells['A']);
        $result['columns'] = count($cells);
        return $result;
    }
}

?>
