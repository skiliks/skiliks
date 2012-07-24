<?php



/**
 * Контроллера документа Excel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelDocumentController extends AjaxController{
    
    protected $_worksheets = array();
    
    protected $_activeWorksheet = false;
    
    protected $_columns = array();
    protected $_columnIndex = array();
    
    protected function _getColumnIndex($column, $worksheetId=false) {
        if (!$worksheetId) $worksheetId = $this->_activeWorksheet;
            Logger::debug('columns : '.var_export($this->_columns[$worksheetId], true));
        return $this->_columns[$worksheetId][$column];
    }
    
    protected function _getColumnByIndex($index, $worksheetId=false) {
        if (!$worksheetId) $worksheetId = $this->_activeWorksheet;
        return $this->_columnIndex[$worksheetId][$index];
    }
    
    /**
     * Определение типа формулы
     * @param string $formula 
     */
    protected function _parseFormulaType($formula) {
        if (preg_match_all("/([A-Z]+)\((.*)\)/", $formula, $matches)) {
            return array(
                'formula' => $matches[1][0],
                'params' => $matches[2][0]
            );
        }
        return false;
    }
    
    protected function _parsePair($formulaInfo) {
        $res = preg_match_all("/(\w)(\d+)\;(\w)(\d+)/", $formulaInfo['params'], $matches); 
        Logger::debug("matches : ".var_export($matches, true));
        if (!isset($matches[1][0])) return array();

        $column = $matches[1][0];
        $string = (int)$matches[2][0];

        $list = array();
        $a = (int)$this->_worksheets[$this->_activeWorksheet][$column][$string]['value'];
        $list[] = $a;
        
        $column = $matches[3][0];
        $string = (int)$matches[4][0];
        $b = (int)$this->_worksheets[$this->_activeWorksheet][$column][$string]['value'];
        $list[] = $b;
        return $list;
    }
    
    protected function _parseRange($formulaInfo) {
        $res = preg_match_all("/(\w)(\d+)\:(\w)(\d+)/", $formulaInfo['params'], $matches); 
        Logger::debug("matches : ".var_export($matches, true));
        if (!isset($matches[1][0])) return false;

        $columnFrom = $matches[1][0];
        $stringFrom = (int)$matches[2][0];
        
        $columnTo = $matches[3][0];
        $stringTo = (int)$matches[4][0];
        
        //Logger::debug("ws : ".var_export($this->_worksheets[$this->_activeWorksheet], true));
        // получаем индексы колонок
        /*$index = 1;
        $indexes = array();
        $columns = array();
        foreach($this->_worksheets[$this->_activeWorksheet] as $column=>$data) {
            $indexes[$column] = $index;
            $columns[$index] = $column;
            $index++;
        }*/
        
        $startIndex = $this->_getColumnIndex($columnFrom);
        $endIndex = $this->_getColumnIndex($columnTo);
        
        //$startIndex = $indexes[$columnFrom];  // индекс колонки, с которой стартуем
        //$endIndex = $indexes[$columnTo];  // индекс колонки, которой финишируем
        
        // определяем колличество колонок
        $columnCount = $endIndex - $startIndex +1;
        
        // определяем колличество строк
        $stringCount = $stringTo - $stringFrom + 1;
        
        Logger::debug("excel columnCount : $columnCount");
        Logger::debug("excel stringCount : $stringCount");
        return array(
            'columnFrom' => $columnFrom,
            'stringFrom' => $stringFrom,
            'columnCount' => $columnCount,
            'stringCount' => $stringCount,
            'columnFromIndex' => $startIndex
        );
        
        
        if ($stringCount > $columnCount) {
            
        }
        
        // вернем список элементов в формате колонка/строка
        $list = array();
        for($columnIndex = $startIndex; $columnIndex <= $endIndex; $columnIndex++) {
            $data = array();
            for($stringIndex = $stringFrom; $stringIndex <= $stringTo; $stringIndex++) {
                $data[] = $columns[$columnIndex].':'.$stringIndex;
            }
            $list[]=$data;
        }
        return $list;
        
        $b = (int)$this->_worksheets[$this->_activeWorksheet][$column][$string]['value'];
        $list[] = $b;
        return $list;
    }
    
    /**
     * Рассчет астосуммы
     * @param type $formulaInfo 
     */
    protected function _calcAutoSum($formulaInfo) {
        $rangeInfo = $this->_parseRange($formulaInfo);

        
        $columnTo = $rangeInfo['columnFromIndex'] + $rangeInfo['columnCount'];
        $stringTo = $rangeInfo['stringFrom'] + $rangeInfo['stringCount'];
        
        $result = array();
        $result['result'] = 1;
        $result['worksheetData'] = array();
        // суммирование поколоночное
        if ($rangeInfo['columnCount'] > $rangeInfo['stringCount']) {
            for($i=$rangeInfo['stringFrom'];$i<$stringTo; $i++ ) {
                $sum = 0;
                for($j=$rangeInfo['columnFromIndex'];$j<$columnTo; $j++ ) {
                    $columnName = $this->_getColumnByIndex($j);
                    $sum+=$this->_worksheets[$this->_activeWorksheet][$columnName][$i]['value'];
                }
                // заносим сумму в след ячейку
                $nextColumn = $this->_getColumnByIndex($columnTo);
                $this->_worksheets[$this->_activeWorksheet][$nextColumn][$i]['value'] = $sum;
                // сохраняем ячейку
                $this->_updateCell(array(
                    'worksheetId' => $this->_activeWorksheet,
                    'column' => $nextColumn,
                    'string' => $i,
                    'value' => $sum
                ));
                // подготовить к отдаче не фронт ячейку
                $result['worksheetData'][] = $this->_worksheets[$this->_activeWorksheet][$nextColumn][$i];
            }
        }
        else {
            // суммирование построчное
            for($j=$rangeInfo['columnFromIndex'];$j<$columnTo; $j++ ) {
                $sum = 0;
                $columnName = $this->_getColumnByIndex($j);
                for($i=$rangeInfo['stringFrom'];$i<$stringTo; $i++ ) {
                    
                    $sum+=$this->_worksheets[$this->_activeWorksheet][$columnName][$i]['value'];
                }
                // заносим сумму в след ячейку
                
                $this->_worksheets[$this->_activeWorksheet][$columnName][$stringTo]['value'] = $sum;
                // сохраняем ячейку
                $this->_updateCell(array(
                    'worksheetId' => $this->_activeWorksheet,
                    'column' => $columnName,
                    'string' => $stringTo,
                    'value' => $sum
                ));
                // подготовить к отдаче не фронт ячейку
                $result['worksheetData'][] = $this->_worksheets[$this->_activeWorksheet][$columnName][$stringTo];
            }
        }
        
        return $result;
    }
    
    protected function _applySum($formulaInfo) {
        $list = $this->_parsePair($formulaInfo);
        if (count($list)>0) {
            return $list[0] + $list[1];
        }
        
        $rangeInfo = $this->_parseRange($formulaInfo);
        //Logger::debug('range info : '.var_export($rangeInfo, true));
        
        $columnTo = $rangeInfo['columnFromIndex'] + $rangeInfo['columnCount'];
        $stringTo = $rangeInfo['stringFrom'] + $rangeInfo['stringCount'];
        
        $sum = 0;
        for($i=$rangeInfo['stringFrom'];$i<$stringTo; $i++ ) {
            for($j=$rangeInfo['columnFromIndex'];$j<$columnTo; $j++ ) {
                $columnName = $this->_getColumnByIndex($j);
                $sum+=$this->_worksheets[$this->_activeWorksheet][$columnName][$i]['value'];
            }
        }
        
        return $sum;
    }
    
    protected function _applyAvg($formulaInfo) {
        if (!preg_match_all("/(\w)(\d+)\:(\w)(\d+)/", $formulaInfo['params'], $matches)) 
        return false;
        
        // у нас в формуле одинаковые строки
        if ($matches[2][0] == $matches[4][0]) {
            
            
            //Logger::debug("test worksheet : ".var_export($this->_worksheets[$this->_activeWorksheet][$matches[1][0]], true));
            $column = $matches[1][0];
            $string = (int)$matches[2][0];
            
            Logger::debug("try to find $column and $string");
            $p1 = (int)$this->_worksheets[$this->_activeWorksheet][$column][$string];
            
            $column = $matches[3][0];
            $string = (int)$matches[4][0];
            $p2 = (int)$this->_worksheets[$this->_activeWorksheet][$column][$string];
            
            Logger::debug("sum : $p1 $p2");
            return Math::avg(array($p1, $p2));
        }
    }
    
    
    
    /**
     * Применение формулы
     * @param string $formula
     * @return mixed
     */
    protected function _parseFormula($formula) {
        Logger::debug("parse formula : $formula");
        
        // определить тип формулы
        $formulaType = $this->_parseFormulaType($formula);
        Logger::debug("formula type: ".var_export($formulaType, true));
        if ($formulaType) {
            switch ($formulaType['formula']) {
                case 'SUM':
                    Logger::debug('parse sum');
                    return $this->_applySum($formulaType);    
                    break;

                case 'AVG':
                    return $this->_applyAvg($formulaType);    
                    break;
            }
        }
    }
    
    /**
     * Возврат воркшита
     * @param int $worksheetId
     * @return array
     */
    protected function _getWorksheet($worksheetId) {
        $result = array();
        
        $cells = ExcelWorksheetCells::model()->byWorksheet($worksheetId)->findAll();
        $columns = array();
        $strings = array();
        
        $data = array();
        $columnIndex = 1;
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
                'rowspan' => $cell->rowspan
            );
            
            $result['worksheetData'][] = $cellInfo;

            $data[$cell->column][$cell->string] = $cellInfo; 
            
            $columns[$cell->column] = 1;
            $strings[$cell->string] = 1;
            
            
            if (!isset($this->_columns[$worksheetId][$cell->column])) {
                $this->_columns[$worksheetId][$cell->column] = $columnIndex;
                $this->_columnIndex[$worksheetId][$columnIndex] = $cell->column;
            
                $columnIndex++;
            }
        }
        
        // запоминаем структуру рабочего листа
        $this->_worksheets[$worksheetId] = $data;
        $this->_activeWorksheet = $worksheetId;
        
        Logger::debug("_getWorksheet data : ".var_export($data, true));
        
        // применим формулы
        foreach($result['worksheetData'] as $index=>$cell) {
            if ($cell['formula'] != '') {
                $value = $this->_parseFormula($cell['formula']);
                if ($value) {
                    $result['worksheetData'][$index]['value'] = $value;
                }
            }
        }

        Logger::debug("strings : ".var_export($strings, true));
        Logger::debug("columns : ".var_export($columns, true));
        $result['strings'] = count($strings);
        $result['columns'] = count($columns);
        
        return $result;
    }
    
    /**
     * получение документа
     * @return 
     */
    public function actionGet() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);  
            if (!$sid) throw new Exception('wrong sid');
            $simId = SessionHelper::getSimIdBySid($sid);
            if (!$simId) throw new Exception("cant find simId by sid {$sid}");
        
            //$document = ExcelDocumentTemplate::model()->byName('Сводный бюджет')->find();
            $document = ExcelDocument::model()->bySimulation($simId)->find();
            if (!$document) {
                throw new Exception('cant find document');
            }
            
            $result = array();
            $result['result'] = 1;
            //$worksheets = ExcelWorksheetTemplate::model()->byDocument($document->id)->findAll();
            $worksheets = ExcelWorksheet::model()->byDocument($document->id)->findAll();
            foreach($worksheets as $worksheet) {
                $result['worksheets'][] = array(
                    'id' => $worksheet->id,
                    'title' => $worksheet->name
                );
            }
            
            
            $worksheetId = $result['worksheets'][0]['id'];
            $result['currentWorksheet'] = $worksheetId;
            
            //$cells = ExcelWorksheetTemplateCells::model()->byWorksheet($worksheetId)->findAll();
            $worksheetData = $this->_getWorksheet($worksheetId);
            $result['worksheetData'] = $worksheetData['worksheetData'];
            $result['strings'] = $worksheetData['strings'];
            $result['columns'] = $worksheetData['columns'];
            
            Logger::debug("actionGet strings : ".var_export($result['strings'], true));
            Logger::debug("actionGet columns : ".var_export($result['columns'], true));
            
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage(),
                'code' => $exc->getCode()
            )));
        }
    }
    
    /**
     * Возврат конкретного worksheet'a
     * @return type 
     */
    public function actionGetWorksheet() {
        $worksheetId = (int)Yii::app()->request->getParam('id', false);  
        $worksheetData = $this->_getWorksheet($worksheetId);
        
        $result = array();
        $result['result'] = 1;
        $result['worksheetData'] = $worksheetData['worksheetData'];
        $result['strings'] = $worksheetData['strings'];
        $result['columns'] = $worksheetData['columns'];
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    protected function _updateCell($params) {
        $cell = ExcelWorksheetCells::model()->findByAttributes(array(
            'worksheet_id' => $params['worksheetId'],
            'string' => $params['string'],
            'column' => $params['column']
        ));
        
        $cell->value = $params['value'];
        $cell->save();
    }
    
    /**
     * Сохранение ячейки
     * @return type 
     */
    public function actionSave() {
        /*
            на вход:
                int     id          id worksheeta'a
                int     string
                string  column
                string  value
                string  comment
                string  formula
                int     colspan
                int     rowspan
        */
        try {
            $worksheetId = (int)Yii::app()->request->getParam('id', false);  
            $string = (int)Yii::app()->request->getParam('string', false);  
            $column = Yii::app()->request->getParam('column', false);  
            $value = Yii::app()->request->getParam('value', false);  
            $comment = Yii::app()->request->getParam('comment', false);  
            $formula = Yii::app()->request->getParam('formula', false);  
            $colspan = (int)Yii::app()->request->getParam('colspan', false);  
            $rowspan = (int)Yii::app()->request->getParam('rowspan', false);  

            $cell = ExcelWorksheetCells::model()->findByAttributes(array(
                'worksheet_id' => $worksheetId,
                'string' => $string,
                'column' => $column
            ));
            if (!$cell) throw new Exception('cant get cell');
            
            // поддержка вычисления формул
            if ($formula != '') {
                // загружаем рабочий лист
                $this->_getWorksheet($worksheetId);
                $value = $this->_parseFormula($formula);
                if (!$value) $value = $formula;
            }
            
            $cell->value = $value;
            $cell->formula = $formula;
            
            /*$cell->comment = $comment;
            $cell->colspan = $colspan;
            $cell->rowspan = $rowspan;*/
            $cell->save();
            
            $result = array();
            $result['result'] = 1;
            $data = array();
            $cell = $this->_worksheets[$this->_activeWorksheet][$column][$string];
            $cell['value'] = $value;
            $data[] = $cell;
            $result['worksheetData'] = $data;
            
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage(),
                'code' => $exc->getCode()
            )));
        }
    }
    
    /**
     * Копирование в clipboard
     */
    public function actionCopy() {
        $worksheetId = (int)Yii::app()->request->getParam('id', false);  
        $range = Yii::app()->request->getParam('range', false);  

        //ExcelClipboard::model()->
            
        /*$sid = Yii::app()->request->getParam('sid', false);  
        if (!$sid) throw new Exception("Не передан sid");
        $simId = SessionHelper::getSimIdBySid($sid);*/
        //ExcelDocumentService::copy('Сводный бюджет', $simId);
    }

    /**
     * Рассчет автосуммы
     * @return type 
     */
    public function actionSum() {
        $worksheetId = (int)Yii::app()->request->getParam('id', false);  
        $range = Yii::app()->request->getParam('range', false);  
     
        $this->_getWorksheet($worksheetId);
        
        $result = array();
        $result['result'] = 1;
        $result['range'] = $range;
        
        $formulaType = array();
        $formulaType['formula'] = 'SUM';
        $formulaType['params'] = $range;
        
        
        return $this->_sendResponse(200, CJSON::encode($this->_calcAutoSum($formulaInfo)));
    }
}

?>
