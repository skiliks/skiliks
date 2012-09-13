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
    
    function __construct($documentId = false) {
        $sid = SessionHelper::getSid();
        $simId = SessionHelper::getSimIdBySid($sid);
        
        /*
        $document = ExcelDocumentModel::model()->bySimulation($simId)->find();
        if (!$document) throw new Exception('cant find document');
        $this->_id = $document->id;

        Logger::debug("ExcelDocument construct");*/
        if ($documentId) {
            // загрузим рабочие листы
            $this->_loadWorksheets($documentId);
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
        
        Logger::debug("getWorksheet : load worksheet $worksheetId");
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
        Logger::debug("map : ".var_export($this->_worksheetMap, true));    
        
        if (!isset($this->_worksheetMap[$name])) return false;
        Logger::debug("getWorksheetByName : $name");    
        
        Logger::debug("map : ".var_export($this->_worksheetMap, true));    
        $worksheetId = $this->_worksheetMap[$name];
        Logger::debug("worksheetId : $worksheetId");    
        
        return $this->getWorksheet($worksheetId);
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
    
    // deprecated!
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
        $t = $profiler->endTimer();
        Logger::debug("_loadWorksheetIfNeeded : $t");
        
        return $this;
    }
    
    public function loadByFile($simId, $fileId) {
        // проверить есть ли такой файл
        $file = MyDocumentsService::existsInSim($simId, $fileId);
        if (!$file) throw new Exception("Для вашей симуляции отсутствует файл");
        
        // проверить есть ли у нас такой документ
        $document = ExcelDocumentModel::model()->bySimulation($simId)->byFile($fileId)->find();
        if (!$document) {
            // пока документа нет, значит надо его залить в симуляцию
            $templateId = $file->template_id;
            // получим документ из шаблонов
            $documentTemplate = ExcelDocumentTemplate::model()->byFile($templateId)->find();
            if (!$documentTemplate) throw new Exception("Немогу загрузить шаблон докмента для $templateId");
            
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
        return $this;
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
        Logger::debug('cells : '.var_export($cells, true));
        
        $excelFormula = new ExcelFormula();
        $columnIndex = 1;
        foreach($cells as $column=>$strings) {
            Logger::debug("column : $column");
            foreach($strings as $string=>$cell) {
                $cell['columnIndex'] = $columnIndex;
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
            
            $columnIndex++;
        }
        $result['worksheetData'] = $worksheetData;
        $result['strings'] = count($cells['A']);
        $result['columns'] = count($cells);
        
        // вернуть информацию о ширине ячейки
        Logger::debug("get ws cell width : {$worksheet->id}");
        $worksheetModel = ExcelWorksheetModel::model()->byId($worksheet->id)->find();
        $result['cellHeight'] = $worksheetModel->cellHeight;
        $result['cellWidth'] = $worksheetModel->cellWidth;
        
        return $result;
    }
}

?>
