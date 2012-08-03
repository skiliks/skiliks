<?php



/**
 * Рабочий лист Excel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelWorksheet {
    
    public $id;
    
    /**
     * Хранит соотношение $columnName=>$columnIndex
     * @var array
     */
    protected $_columns = array();
    
    protected $_columnIndex = array();
    
    protected $_data;
    
    /**
     *
     * @return array
     */
    public function getCells() {
        return $this->_data;
    }
    
    public function getCell($column, $string) {
        return $this->_data[$column][$string];
    }
    
    public function replaceCell($cell) {
        return $this->_data[$cell['column']][$cell['string']] = $cell;
    }
    
    public function getColumnIndex($columnName) {
        return (isset($this->_columns[$columnName])) ? $this->_columns[$columnName] : null;
    }
    
    public function getColumnNameByIndex($columnIndex) {
        return (isset($this->_columnIndex[$columnIndex])) ? $this->_columnIndex[$columnIndex] : null;
    }
    
    public function load($worksheetId) {
        Logger::debug("load ws : $worksheetId");
        $this->id = $worksheetId;
        
        $data = Cache::get('ws'.$worksheetId);
        if (!$data) {
            // у нас пока ничего не закешировано - значит придется загрузить
            $cells = ExcelWorksheetCells::model()->byWorksheet($worksheetId)->findAll();
            $data = array();
            foreach($cells as $cell) {
                $cellInfo = array(
                    'id' => $cell->id,
                    'string' => $cell->string,
                    'column' => $cell->column,
                    'value' => $cell->value,
                    'read_only' => 0, //$cell->read_only,
                    'comment' => (!is_null($cell->comment)) ? $cell->comment : '',
                    'formula' => $cell->formula,
                    'colspan' => $cell->colspan,
                    'rowspan' => $cell->rowspan,
                    'worksheetId' => $worksheetId,
                    'bold' => $cell->bold,
                    'color' => $cell->color,
                    'font' => $cell->font,
                    'fontSize' => $cell->fontSize,
                );
                $data[$cell->column][$cell->string] = $cellInfo; 
            }
            // запомним в кеше
            Cache::put('ws'.$worksheetId, $data);
        }
        
        //Logger::debug("ws data: ".var_export($data, true));
        
        // создать соотв индексов
        $columnIndex = 1;
        foreach($data as $column=>$someInfo) {
            $this->_columns[$column] = $columnIndex;
            $this->_columnIndex[$columnIndex] = $column;
            $columnIndex++;
        }
        Logger::debug("ws columns : ".var_export($this->_columns, true));
        Logger::debug("ws index : ".var_export($this->_columnIndex, true));
        $this->_data = $data;
        
        return $data;
    }
    
    public function getValueByName($cellName) {
        Logger::debug("ws {$this->id} getValueByName $cellName");
        Logger::debug('name : '.var_export($cellName, true));
        if (is_numeric($cellName)) {
            Logger::debug('is number');
            return $cellName;
        }
        
        $worksheetName = false;
        if (!strstr($cellName, '!')) {
            preg_match_all("/([A-Z]+)(\d+)/", $cellName, $matches); 
            Logger::debug("matches : ".var_export($matches, true));
            if (!isset($matches[1][0])) return false;
            $column = $matches[1][0];
            $string = (int)$matches[2][0];
        }
        else {
            Logger::debug("match : $cellName");
            if (preg_match_all("/(\w*)!([A-Z]+)(\d+)/u", $cellName, $matches)) {
                Logger::debug("matches : ".var_export($matches, true));
                
                $worksheetName = $matches[1][0];
                $column = $matches[2][0];
                $string = (int)$matches[3][0];
            }
        }
        

        Logger::debug("ws name : $worksheetName");
        // у нас ссылка на другой воркшит
        if ($worksheetName) {
            return ExcelFactory::getDocument()->getWorksheetByName($worksheetName)->getValueByName($column.$string);
        }
      
        if(!isset($this->_data[$column][$string])) {
            Logger::debug("fuck $column $string");
        }
        $cell = $this->_data[$column][$string];
        
        
        if ($cell['value']=='') {
            // смотрим формулу
            if ($cell['formula']!='') {
                $formula = new ExcelFormula();
                $formula->setWorksheet($this);
                $value = $formula->parse($cell['formula']);
                Logger::debug("return value $value");
                return $value;
            }
        }
        
        Logger::debug("return value {$cell['value']}");
        if ($cell['value'] == '') return 0;
        return $cell['value'];
    }
    
    public function saveToCache() {
        // сохраним информацию в кеше
        Cache::put('ws'.$this->id, $this->_data);
    }
    
    public function updateCellDb($cell) {
        $cellModel = ExcelWorksheetCells::model()->findByAttributes(array(
            'worksheet_id' => $this->id,
            'string' => $cell['string'],
            'column' => $cell['column']
        ));
        if (!$cellModel) throw new Exception('cant get cell');
        $cellModel->value = $cell['value'];
        $cellModel->formula = $cell['formula'];
        $cellModel->read_only = $cell['read_only'];
        $cellModel->save();
    }
    
    public function explodeCellName($cellName) {
        if (preg_match_all("/([A-Za-zА-Яа-я!]+)(\d+)/", $cellName, $matches)) {
            $result = array(
                'column' => $matches[1][0],
                'string' => (int)$matches[2][0]
            );
            return $result;
        } 
        return false;    
    }
}

?>
