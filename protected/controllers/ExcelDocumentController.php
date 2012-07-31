<?php

function replaceVars2($formula, $vars ) {
        Logger::debug("_replaceVars2 vars : ".var_export($vars, true));
        function callback($str) {
            global $vars;
            Logger::debug("callback vars : ".var_export($vars, true));
            if (isset($vars[$str[1]]))
                return $vars[$str[1]];
            return '2';
        }
        return preg_replace_callback("/(\w*\!*\w+\d+)/u", 'callback', $formula);
    }

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
    
    /**
     * Кеш имен воркшитов
     * @var type 
     */
    protected $_wsNamesCache = array();
    
    public static $vars;
    
    /**
     * Получение идентификатора воркшита по его имени
     * @param string $worksheetName 
     */
    protected function _getWorksheetIdByName($worksheetName) {
        if (isset($this->_wsNamesCache[$worksheetName])) {
            return $this->_wsNamesCache[$worksheetName];
        }
        
        $worksheet = ExcelWorksheet::model()->byName($worksheetName)->find();
        if ($worksheet) {
            $this->_wsNamesCache[$worksheetName] = $worksheet->id;
            return $worksheet->id;
        }
    }
    
    protected function _loadWorksheetIfNeeded($worksheetId) {
        if (!isset($this->_worksheets[$worksheetId])) {
            $this->_getWorksheet($worksheetId, false);
        }
    }
    
    protected function _getColumnIndex($column, $worksheetId=false) {
        Logger::debug("_getColumnIndex : $column, $worksheetId");
        if (!$worksheetId) $worksheetId = $this->_activeWorksheet;
            //Logger::debug('columns : '.var_export($this->_columns[$worksheetId], true));
            if (!isset($this->_columns[$worksheetId][$column])) {
               // Logger::debug("cant find : $worksheetId, $column"); die();
            }
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
        if (preg_match_all("/=(\w+)\((.*)\)/u", $formula, $matches)) {
            //Logger::debug("_parseFormulaType : ".var_export($matches, true));
            return array(
                'formula' => $matches[1][0],
                'params' => $matches[2][0]
            );
        }
        
        if (preg_match_all("/=(.*)/u", $formula, $matches)) {
            return array(
                'expr' => $matches[1][0]
            );
        } 
        
        return false;
    }
    
    protected function _explodeCellName($cellName) {
        if (preg_match_all("/([A-Za-zА-Яа-я!]+)(\d+)/", $cellName, $matches)) {
            $result = array(
                'column' => $matches[1][0],
                'string' => (int)$matches[2][0]
            );
            return $result;
        } 
        return false;    
    }
    
    /**
     * Парсинг пар вида A1;B2;E10
     * @param string $range
     * @return array
     */
    protected function _parsePair($range) {
        if (!strstr($range, ';')) return array();
        $data = explode(';', $range);
        Logger::debug("data : ".var_export($data, true));
        if (count($data) == 0) return array();
        
        $list = array();
        foreach($data as $cellName) {
            $cellInfo = $this->_explodeCellName($cellName);
            Logger::debug("cellInfo : ".var_export($cellInfo, true));
            $column = $cellInfo['column'];
            $string = $cellInfo['string'];
            
            $value = $this->_getCellValue($column, $string);
            if ($value != '') $list[] = $value;
        }
        
        return $list;
    }
    
    protected function _parseRange($range) {
        //Logger::debug("_parseRange : $range");
        $res = preg_match_all("/(\w)(\d+)\:(\w)(\d+)/", $range, $matches); 
        //Logger::debug("matches : ".var_export($matches, true));
        if (!isset($matches[1][0])) return false;

        $columnFrom = $matches[1][0];
        $stringFrom = (int)$matches[2][0];
        
        $columnTo = $matches[3][0];
        $stringTo = (int)$matches[4][0];
        
        
        $startIndex = $this->_getColumnIndex($columnFrom); // индекс колонки, с которой стартуем
        $endIndex = $this->_getColumnIndex($columnTo); // индекс колонки, которой финишируем
        
        
            // определяем колличество колонок
            $columnCount = ($endIndex - $startIndex) +1;
            // определяем колличество строк
            $stringCount = $stringTo - $stringFrom + 1;
        
        
        //Logger::debug("excel columnCount : $columnCount");
        //Logger::debug("excel stringCount : $stringCount");
        return array(
            'columnFrom' => $columnFrom,
            'stringFrom' => $stringFrom,
            'columnCount' => $columnCount,
            'stringCount' => $stringCount,
            'columnFromIndex' => $startIndex
        );
    }
    
    protected function _parseRangeToArray($range) {
        $rangeInfo = $this->_parseRange($range);
        
        $list = array();
        $columnTo = $rangeInfo['columnFromIndex'] + $rangeInfo['columnCount'];
        $stringTo = $rangeInfo['stringFrom'] + $rangeInfo['stringCount'];
        // бежим по колонкам
        for($columnIndex = $rangeInfo['columnFromIndex']; $columnIndex < $columnTo; $columnIndex++) {
            $columnName = $this->_getColumnByIndex($columnIndex);
            for($stringIndex = $rangeInfo['stringFrom']; $stringIndex < $stringTo; $stringIndex++) {
                $value = $this->_getCellValue($columnName, $stringIndex);
                if ($value != '') $list[] = $value;
            }
        }
        
        return $list;
    }
    
    /**
     * Рассчет астосуммы
     * @param type $formulaInfo 
     */
    protected function _calcAutoSum($formulaInfo) {
        $rangeInfo = $this->_parseRange($formulaInfo['params']);

        
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
    
    /**
     * Возвращает ячейку
     * @param string $column колонка
     * @param int $string строка
     * @param int $worksheetId идентификатор рабочего листа
     * @return array
     */
    protected function _getCell($column, $string, $worksheetId=false) {
        if (!$worksheetId) $worksheetId = $this->_activeWorksheet;
        if (!isset($this->_worksheets[$worksheetId][$column][$string])) return false;
        return $this->_worksheets[$worksheetId][$column][$string];
    }
    
    protected function _getCellValue($column, $string, $worksheetId=false) {
        $cell = $this->_getCell($column, $string, $worksheetId);
        //Logger::debug("_getCellValue cell : ".var_export($cell, true));
        if (!$cell) return false;
        return $cell['value'];
    }
    
    protected function _setCell($column, $string, $cell, $worksheetId=false) {
        if (!$worksheetId) $worksheetId = $this->_activeWorksheet;
        $this->_worksheets[$worksheetId][$column][$string] = $cell;
    }
    
    /**
     * Получение значения переменной по ее имени
     * @param string $cellName
     * @param int $worksheetId
     * @return mixed
     */
    protected function _getCellValueByName($cellName, $worksheetId=false) {
        $worksheetName = false;
        
        //Logger::debug("_getCellValueByName : $cellName");
        
        if (!strstr($cellName, '!')) {
            preg_match_all("/(\w)(\d+)/", $cellName, $matches); 
            if (!isset($matches[1][0])) return false;
            $column = $matches[1][0];
            $string = (int)$matches[2][0];
        }
        else {
            if (preg_match_all("/(\w*)!([A-Z]+)(\d+)/u", $cellName, $matches)) {
                //Logger::debug("matches : ".  var_export($matches, true));
                
                $worksheetName = $matches[1][0];
                $column = $matches[2][0];
                $string = (int)$matches[3][0];
                
                //Logger::debug("wsName : $worksheetName");
            }
        }
        
        if (!$worksheetId) $worksheetId = $this->_activeWorksheet;
        
        // у нас ссылка на другой воркшит
        if ($worksheetName) {
            $worksheetId = $this->_getWorksheetIdByName($worksheetName);
            if ($worksheetId) {
                $this->_loadWorksheetIfNeeded($worksheetId);
            }
            Logger::debug("wsId : $worksheetId");
        }
        
        Logger::debug("column $column string $string wsId $worksheetId wsName $worksheetName");
        $cell = $this->_getCell($column, $string, $worksheetId);
        Logger::debug('cell : '.var_export($cell, true));
        
        // если такой ячейки не существует
        if (!$cell) return null;
        
        
        if ($cell['value']=='') {
            // смотрим формулу
            if ($cell['formula']!='') {
                return $this->_parseFormula($cell['formula']);
            }
        }
        return $cell['value'];
        //Logger::debug("found value ".var_dump($value, true));
        return 0;
    }
    
    protected function _isNumber($value) {
        return preg_match_all("/\d+/", $value, $matches);
    }
    
    protected function _validateFormula($formula) {
        $formulaInfo = $this->_parseFormulaType($formula);
        if (isset($formulaInfo['expr'])) {
            if (!preg_match_all("/\d+/u", $formulaInfo['expr'], $matches)) {
                return array('result'=>false, 'message'=>'Формула введена неправильно. Повторите ввод');
            }
        }
        
        $vars = $this->_explodeFormulaVars($formula);
        foreach($vars as $var) {
            Logger::debug("validate var : $var");
            $value = $this->_getCellValueByName($var);
            if (is_null($value)) return array('result'=>false, 'message'=>'Формула введена неправильно. Повторите ввод');
            
            if ($value=='')  return array('result'=>true, 'value'=>0);
            
            Logger::debug("value : $value");
            if (!is_numeric($value)) return array(
            //if ($this->_isNumber($value)) return array(    
                'result' => false,
                'message' => "В ячейке $var введено текстовое значение. Повторите ввод"
            ); //throw new Exception("В ячейке $var введено не текстовое значение. Повторите ввод".);
            //if (!$this->_isNumber($value)) throw new Exception("В ячейке $var введено не текстовое значение. Повторите ввод");
        }
        return array('result'=>true);
    }


    protected function _explodeFormulaVars($formula) {
        preg_match_all("/([A-Za-А-Яа-я!]+\d+)/", $formula, $matches); 
        if (isset($matches[0][0])) return $matches[0];
        return array();
    }
    
    protected function _hasLinkVar($expr) {
        return preg_match_all("/([A-Za-А-Яа-я]+\!\w+\d+)/", $expr, $matches); 
    }
    
    protected function _parseExpr($expr) {
        $expression = $expr;
        Logger::debug('_parseExpr : '.$expr);
        // заменим переменные в выражении
        $vars = $this->_explodeFormulaVars($expr);
        Logger::debug('vars : '.var_export($vars, true));
        // Если у нас есть переменные

        foreach($vars as $varName) {
            Logger::debug("_getCellValueByName : $varName");
            $value = $this->_getCellValueByName($varName);
            Logger::debug("value : ".var_export($value, true));
            if ($value=='') $value=0;
            //if ($value) {
                $expr = str_replace($varName, $value, $expr);
            //}
        }

        
        $expr = str_replace(',', '.', $expr);
        
        // если нечего эвалить
        if (!$this->_isExpression($expr)) return $expr;
        
        $a = null;
        Logger::debug("eval : $expr");
        @eval ('$a='.$expr.';');
        //Logger::debug("a = $a");
        
        if (is_null($a)) return null;//'='.$expression;
        return Strings::formatThousend($a);
    }
    
    protected function _isExpression($expr) {
        return preg_match_all("/([+\-\*\/]+)/u", $expr, $matches); 
    }
    
    /**
     * Рассчет суммы.
     * @param type $formulaInfo
     * @return type 
     */
    protected function _applySum($formulaInfo) {
        Logger::debug('_applySum');
        // проверяем, а вдруг у нас сумма вида A1+B2
        if (strstr($formulaInfo['params'], '+')) {
            $list = explode('+', $formulaInfo['params']);
            if (count($list)>0) {
                foreach($list as $index=>$cellName) {
                    $list[$index] = $this->_getCellValueByName($cellName);
                }
                return array_sum($list); 
            }
        }
        
        // если у нас пара - вычисляем ее
        Logger::debug('parse pair');
        $list = $this->_parsePair($formulaInfo['params']);
        if (count($list)>0) return array_sum($list); 
        
        // если у нас диапазон
        Logger::debug('parse range : '.$formulaInfo['params']);
        $rangeInfo = $this->_parseRange($formulaInfo['params']);
        Logger::debug('range info : '.var_export($rangeInfo, true));

        // рассчет квадрата по диапазону
        $columnTo = $rangeInfo['columnFromIndex'] + $rangeInfo['columnCount'];
        $stringTo = $rangeInfo['stringFrom'] + $rangeInfo['stringCount'];
        $sum = 0;
        for($i=$rangeInfo['stringFrom'];$i<$stringTo; $i++ ) {
            for($j=$rangeInfo['columnFromIndex'];$j<$columnTo; $j++ ) {
                $columnName = $this->_getColumnByIndex($j);
                $value = $this->_getCellValue($columnName, $i);
                Logger::debug("cur value : $value");
                $sum += $value;
            }
        }
        
        return $sum;
    }
    
    
    
    /**
     * Расчет среднего значения
     * @param type $formulaInfo
     * @return type 
     */
    protected function _applyAvg($formulaInfo) {
        $list = $this->_parsePair($formulaInfo['params']);
        Logger::debug("list : ".var_export($list, true));
        if (count($list)>0) {
            return Math::avg($list);
        }
        
        Logger::debug("avg  _parseRangeToArray : ".$formulaInfo['params']);
        $list = $this->_parseRangeToArray($formulaInfo['params']);
        Logger::debug("list : ".var_export($list, true));
        return Math::avg($list);
    }
    
    
    
    /**
     * Применение формулы
     * @param string $formula
     * @return mixed
     */
    protected function _parseFormula($formula) {
        Logger::debug("parse formula : $formula");
        
        // определить тип формулы
        $formulaInfo = $this->_parseFormulaType($formula);
        if (!$formulaInfo) return $formula; // это просто значение
        
        Logger::debug("formula type: ".var_export($formulaInfo, true));
        // если не удалось определить информацию о формуле
        if (isset($formulaInfo['expr'])) {
            //Logger::debug("try to parse expr : ".$formulaInfo['expr']);
            return $this->_parseExpr($formulaInfo['expr']);
        }
        
        
        $formulaType = $formulaInfo['formula'];
        
        if (($formulaType == 'сумм') || ($formulaType == 'sum') || ($formulaType == 'SUM') || ($formulaType == 'СУММ')) {
            //Logger::debug('parse sum');
            return $this->_applySum($formulaInfo);    
        }
        
        if (($formulaType == 'среднее') || ($formulaType == 'average') || ($formulaType == 'СРЗНАЧ')) {
            //Logger::debug('parse avg');
            return $this->_applyAvg($formulaInfo);    
        }
        
        return null;  // неизвестно как парсить
    }
    
    protected function _processValue($value) {
        if (is_numeric($value)) {
            $showDecimals = false;
            if (strstr($value, '.')) $showDecimals = true;
            return Strings::formatThousend($value, $showDecimals);
        }
        return $value;
    }
    
    /**
     * Возврат воркшита
     * @param int $worksheetId
     * @return array
     */
    protected function _getWorksheet($worksheetId, $activateWorksheet = true) {
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
                'rowspan' => $cell->rowspan,
                'worksheetId' => $worksheetId
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
        if ($activateWorksheet)$this->_activeWorksheet = $worksheetId;
        
        //Logger::debug("_getWorksheet data : ".var_export($data, true));
        
        // применим формулы
        foreach($result['worksheetData'] as $index=>$cell) {
            if ($cell['formula'] != '') {
                $value = $this->_parseFormula($cell['formula']);
                if ($value) {
                    $result['worksheetData'][$index]['value'] = $value;
                }
            }
            
            if ($this->_hasLinkVar($cell['formula'])) {
                $result['worksheetData'][$index]['read_only'] = 1;
            }
            
            // постобработка
            $result['worksheetData'][$index]['value'] = $this->_processValue($result['worksheetData'][$index]['value']);
        }

        //Logger::debug("strings : ".var_export($strings, true));
        //Logger::debug("columns : ".var_export($columns, true));
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
        if (isset($params['formula']))  $cell->formula = $params['formula'];
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
            
            $message = false;
            //$formula = Strings::toUtf8($formula);

            $cell = ExcelWorksheetCells::model()->findByAttributes(array(
                'worksheet_id' => $worksheetId,
                'string' => $string,
                'column' => $column
            ));
            if (!$cell) throw new Exception('cant get cell');
            
            // поддержка вычисления формул
            if ($formula != '') {
                
                
                
                Logger::debug("found formula : $formula");
                // загружаем рабочий лист
                $this->_getWorksheet($worksheetId);
                
                // проверяем формулу
                $validationResult = $this->_validateFormula($formula);
                if (isset($validationResult['message'])) {
                    $message = $validationResult['message'];
                }
                    
                if (isset($validationResult['value'])) {
                    $value = $validationResult['value'];
                }
                
                $value = $this->_parseFormula($formula);
                if (is_null($value)) {
                    //throw new Exception('Формула введена неправильно. Повторите ввод');
                }
                
                Logger::debug("value after process formula : $value");
                //if (!$value) $value = $formula;
                if ($value) $value = $this->_processValue($value);
                Logger::debug("value after process value : $value");
                
                $cell->read_only = (int)(bool)$this->_hasLinkVar($formula);
            }
            
            $cell->value = $value;
            $cell->formula = $formula;
            $cell->save();
            
            $result = array();
            $result['result'] = 1;
            $data = array();
            $cellItem = $this->_getCell($column, $string); //$this->_worksheets[$this->_activeWorksheet][$column][$string];
            $cellItem['value'] = $value;
            $cellItem['formula'] = $formula;
            $cellItem['read_only'] = $cell->read_only;
            $data[] = $cellItem;
            $result['worksheetData'] = $data;
            
            if ($message) $result['message'] = $message;
            
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
        
        
        return $this->_sendResponse(200, CJSON::encode($this->_calcAutoSum($formulaType)));
    }
    
    /**
     * Сдвигает переменные в формуле
     * @param string $formula формула, которую надо обработать 
     * @param string $column
     * @param int $string
     */
    protected function _shiftFormulaVars($formula, $column, $string, $range) {
        $formulaInfo = $this->_parseFormulaType($formula);
        
        $vars = $this->_explodeFormulaVars($formula);
        
        // смещение по строке
        $stringShift = $string - $range['stringFrom'];
        
        // смещение по столбцу
        $columnIndex = $this->_getColumnIndex($column);
        $columnShift = $columnIndex - $range['columnFromIndex'];
        
        
        /**
        // пробуем получить переменные
        $delimiter = false;
        if (isset($formulaInfo['params'])) {
            if (strstr($formulaInfo['params'], ':')) {
                $vars = explode(':', $formulaInfo['params']);
                $delimiter = ':';
            }
            if (strstr($formulaInfo['params'], ';')) {
                $vars = explode(';', $formulaInfo['params']);
                $delimiter = ';';
            }
        }
        // проверим а вдруг у нас просто ссылка на ячейку
        if (isset($formulaInfo['expr'])) {
            $vars = $this->_explodeFormulaVars($formulaInfo['expr']);
        }*/
        
        Logger::debug('vars :'.var_export($vars, true));
        if (count($vars)==0) return $formula; // нечего сдвигать
        
        $columnIndex = $this->_getColumnIndex($column);
        
        $newVars = array();
        foreach($vars as $index=>$var) {
            $varInfo = $this->_explodeCellName($var);
            $curColumn = $varInfo['column'];
            $curString = $varInfo['string'];
            $curColumnIndex = $this->_getColumnIndex($curColumn);
            
            $curColumnIndex = $curColumnIndex + $columnShift;
            
            $curColumn = $this->_getColumnByIndex($curColumnIndex);
            
                $curString = $curString + $stringShift;
            
            //++$vars[$index] = $curColumn.$curString;
            $newVars[$var] = $curColumn.$curString;
        }
        
        Logger::debug('new vars :'.var_export($newVars, true));
        Logger::debug("replace vars in formula : $formula");
        return $this->_replaceVars2($formula, $newVars);
        
        // собираем формулу
        if ($delimiter)
            return '='.$formulaInfo['formula'].'('.implode($delimiter, $vars).')';
        
        return '='.$vars[0];
        
        //$columnName = $this->_getColumnByIndex($columnIndex, $worksheetId);
    }
    
    /**
     * Вставка из clipboard.
     * @return type 
     */
    public function actionPaste() {
        Logger::debug("actionPaste");
        // куда вставлять
        $worksheetId = (int)Yii::app()->request->getParam('id', false);  
        
        // откуда вставлять
        $fromWorksheetId = (int)Yii::app()->request->getParam('fromId', false);  
        
        
        $string = (int)Yii::app()->request->getParam('string', false);  
        
        $column = Yii::app()->request->getParam('column', false);  
        
        // Диапазон, который мы копируем
        $range = Yii::app()->request->getParam('range', false);  
        
        // загружаем воркшит
        $this->_getWorksheet($fromWorksheetId);
        
        if ($worksheetId != $fromWorksheetId) {
            $this->_getWorksheet($worksheetId);
        }
        
        $rangeInfo = $this->_parseRange($range);
        /*
        'columnFrom' => $columnFrom,
            'stringFrom' => $stringFrom,
            'columnCount' => $columnCount,
            'stringCount' => $stringCount,
            'columnFromIndex' => $startIndex*/
        
        $clipboard = array();
        // запомним состояние ячеек
        $columnTo = $rangeInfo['columnFromIndex'] + $rangeInfo['columnCount']; 
        $stringTo = $rangeInfo['stringFrom'] + $rangeInfo['stringCount'];
        
        $columnIndex=0; $stringIndex = 0;
        for($j = $rangeInfo['columnFromIndex']; $j<$columnTo; $j++) {
            Logger::debug("get column name index $j ws $fromWorksheetId");
            $columnName = $this->_getColumnByIndex($j, $fromWorksheetId);
            
            $stringIndex = 0;
            for($i = $rangeInfo['stringFrom']; $i<$stringTo; $i++) {
                $clipboard[$columnIndex][$stringIndex] = $this->_getCell($columnName, $i, $fromWorksheetId); //$this->_worksheets[$fromWorksheetId][$columnName][$i];
                $stringIndex++;
            }
            
            $columnIndex++;
        }
        
        // Возврат результата
        $columnIndex = $this->_getColumnIndex($column);
        
        $columnTo = $columnIndex + $rangeInfo['columnCount']; 
        $stringTo = $string + $rangeInfo['stringCount'];
        
        
        $result = array();
        $result['result'] = 1;
        $result['worksheetData'] = array();
        
        Logger::debug('clipboard : '.var_export($clipboard, true));
        
        $stringIndex = $string;
        for($j=0; $j<$rangeInfo['columnCount'];$j++) {
            
            $columnName = $this->_getColumnByIndex($columnIndex, $worksheetId);
            $stringIndex = $string;
            for($i = 0; $i<$rangeInfo['stringCount']; $i++) {
                $cell = $clipboard[$j][$i];
                
                // обработать формулу
                if ($cell['formula']!='') {
                    $cell['formula'] = $this->_shiftFormulaVars($cell['formula'], $column, $string, $rangeInfo);
                    Logger::debug("formula after shifting : ".$cell['formula']);
                    // пересчитаем формулу
                    $cell['value'] = $this->_parseFormula($cell['formula']);
                }
                
                
                $cell['column'] = $columnName;
                $cell['string'] = $stringIndex;
                
                $result['worksheetData'][] = $cell;
                
                // запомним результат
                $params = array(
                    'worksheetId' => $worksheetId,
                    'column' => $columnName,
                    'string' => $stringIndex,
                    'value' => $cell['value'],
                    'formula' => $cell['formula']
                );
                $this->_updateCell($params);
                
                $stringIndex++;
            }
            $columnIndex++;
        }
        
        
        
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    protected function _replaceVars2($formula, $vars ) {
        Logger::debug("_replaceVars2 vars : ".var_export($vars, true));
        ExcelDocumentController::$vars = $vars;
        function callback($str) {
            //global $vars;
            Logger::debug("callback vars : ".var_export(ExcelDocumentController::$vars, true));
            if (isset(ExcelDocumentController::$vars[$str[1]]))
                return ExcelDocumentController::$vars[$str[1]];
            return '2';
        }
        return preg_replace_callback("/(\w*\!*\w+\d+)/u", 'callback', $formula);
    }
    
    protected function _replaceVars($expr, $newVars) {
        $var='';
        $str = '';
        $exprLen = strlen($expr);
        $i=0;
        while($i<$exprLen) {
            $s = $expr[$i];
            if (!preg_match("/[=+\-\*\/\(\)\;\:]/", $s)) {
                $var .= $s;
                //echo"var = $var";
                $i++;
            }
            else {
                //$str .= $s;
                //echo"var = $var";
                if (isset($newVars[$var])) {
                    $str.=$newVars[$var].$s;
                    $i=$i+(strlen($var)-1);
                    $var = '';
                    $i--;
                }
                else {
                    $i++;    
                    $str.=$s;
                }

            }

            //echo("str=$str<br>");
        }
        //echo("var=$var");
        if (isset($newVars[$var])) {
                    $str.=$newVars[$var];

        }
        return $str;
    }
    
    /**
     * Протягивание
     */
    public function actionDrawing() {
        try {
            Logger::debug("actionDrawing");
            $worksheetId = (int)Yii::app()->request->getParam('id', false);  
            $string = (int)Yii::app()->request->getParam('string', false);  
            $column = Yii::app()->request->getParam('column', false);  
            $target = Yii::app()->request->getParam('target', false);  

            Logger::debug("target : $target");
            $targetInfo = $this->_explodeCellName($target);
            Logger::debug("targetInfo : ".var_export($targetInfo, true));
            
            // загрузить рабочий лист
            $this->_getWorksheet($worksheetId);
            
            Logger::debug("get cell $column, $string");
            $cell = $this->_getCell($column, $string);
            if (!$cell) throw new Exception('cant find cell');
            if ($cell['formula'] == '') throw new Exception('no formula to apply');
            
            $formula = $cell['formula'];
            

            $result = array();
            $result['result'] = 1;
            Logger::debug("compare {$targetInfo['column']} with $column");
            if ($targetInfo['column'] == $column) {
                Logger::debug("vertical drawing");
                Logger::debug("formula : $formula");
                
                $step = 0;
                // вертикальное протягивание
                for($i = $string; $i<=$targetInfo['string']; $i++) {
                    
                    // выбираем переменные из формулы
                    $vars = $this->_explodeFormulaVars($formula);
                    Logger::debug("vars : ".var_export($vars, true));
                    
                    $newFormula = $formula;
                    $newVars = array();
                    foreach($vars as $varName) {
                        $cellInfo = $this->_explodeCellName($varName);
                        $cellString = (int)$cellInfo['string'];
                        $cellString+=$step;
                        //$cellInfo['string'] = $i;
                        
                        $newVars[$varName] = $cellInfo['column'].$cellString;
                        //$newFormula = str_replace($varName, $cellInfo['column'].$cellString, $newFormula);
                    }
                    $step++;
                    $newFormula = $this->_replaceVars($formula, $newVars);
                    
                    Logger::debug("new vars : ".var_export($newVars, true));
                    /*foreach($newVars as $oldVar=>$newVar) {
                        $newFormula = str_replace($oldVar, $newVar, $newFormula);
                    }*/
                    
                    
                    $value = $this->_parseFormula($newFormula);
                    
                    Logger::debug("get cell $column, $i");
                    $cell = $this->_getCell($column, $i);
                    $cell['value'] = $value;
                    $cell['formula'] = $newFormula;
                    
                    // изменим ячеку
                    $this->_setCell($column, $i, $cell);
                    $this->_updateCell($cell);
                    
                    $result['worksheetData'][] = $cell;
                }
            }
            else {
                Logger::debug("horizontal drawing");
                // горизонтальное протягивание
                $columnFromIndex = $this->_getColumnIndex($column)+1;
                $columnToIndex = $this->_getColumnIndex($targetInfo['column']);
                
                
                // выбираем переменные из формулы
                $formulaInfo = $this->_parseFormulaType($formula);
                Logger::debug('formulaInfo : '.var_export($formulaInfo, true));
                //$vars = explode(';', $formulaInfo['params']);
                if (isset($formulaInfo['params'])) $formulaContent = $formulaInfo['params'];
                if (isset($formulaInfo['expr'])) $formulaContent = $formulaInfo['expr'];
                $vars = $this->_explodeFormulaVars($formulaContent);
                
                // бежим по колонкам
                $inc = 1;
                for($i = $columnFromIndex; $i<=$columnToIndex; $i++) {
                    
                    //$vars = $this->_explodeFormulaVars($formula);
                    //$newFormula = $formula;
                    $newVars = array();
                    foreach($vars as $varName) {
                        $cellInfo = $this->_explodeCellName($varName);
                        
                        // сдвигаем колонку вправо
                        $curColumnIndex = $this->_getColumnIndex($cellInfo['column']);
                        $curColumnIndex = $curColumnIndex + $inc;
                        $cellInfo['column'] = $this->_getColumnByIndex($curColumnIndex);
                        
                        $newVars[$varName] = $cellInfo['column'].$cellInfo['string'];
                        //$newFormula = str_replace($varName, $cellInfo['column'].$cellInfo['string'], $newFormula);
                    }
                    Logger::debug("new vars : ".var_export($newVars, true));
                    
                    $newFormula = $this->_replaceVars($formula, $newVars);
                    /*$newFormula = $formula;
                    foreach($newVars as $oldVar=>$newVar) {
                        $newFormula = str_replace($oldVar, $newVar, $newFormula);
                    }*/
                    //$newFormula = '='.$formulaInfo['formula'].'('.implode(';', $newVars).')';
                    
                    Logger::debug("new formula = $newFormula");
                    
                    $value = $this->_parseFormula($newFormula);
                    
                    Logger::debug("new formula : $newFormula");
                    $column = $this->_getColumnByIndex($i);
                    $cell = $this->_getCell($column, $string);
                    $cell['value'] = $value;
                    $cell['formula'] = $newFormula;
                    
                    // изменим ячеку
                    $this->_setCell($column, $string, $cell);
                    $this->_updateCell($cell);
                    
                    $result['worksheetData'][] = $cell;
                    
                    $inc++;
                }
            }

            return $this->_sendResponse(200, CJSON::encode($result));
        
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage(),
                'code' => $exc->getCode()
            )));
        }
    }
}

?>
