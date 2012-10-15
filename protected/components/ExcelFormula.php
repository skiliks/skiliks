<?php



/**
 * Description of ExcelFormula
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelFormula {
    
    private $_vars;
    
    private $_worksheet = false;
    
    private $_document = false;
    
    private function _getWorksheet() {
        if ($this->_worksheet) return $this->_worksheet;
        return ExcelFactory::getDocument()->getActiveWorksheet();
    }
    
    public function setWorksheet($worksheet) {
        $this->_worksheet = $worksheet;
    }
    
    private function _getDocument() {
        if ($this->_document) return $this->_document;
        return ExcelFactory::getDocument();
    }
    
    public function setDocument($document) {
        $this->_document = $document;
    }
    
    public function callback($matches) {
        
        $formula = $matches[1];
        $params = $matches[2];
        
        if ($formula == 'SUM') {
            return ExcelSum::apply($params);
        }    
        
        if ($formula == 'AVERAGE') {
            return ExcelAvg::apply($params);
        }    
    }
    
    public function explodeFormulaVars($formula) {
        preg_match_all("/([A-Za-zА-Яа-я\!]+\d+)/u", $formula, $matches); 
        if (isset($matches[0][0])) return $matches[0];
        return array();
    }
    
    function replaceVarsCallback($str) {
        Logger::debug("str : ".var_export($str, true));
        $varName = $str[1];
        if (is_numeric($varName)) return $varName;
        
        if (is_array($this->_vars) && isset($this->_vars[$str[1]]))
            return $this->_vars[$str[1]];
        
        if (strstr($varName, '!')) {
            $data = explode('!', $varName);
            Logger::debug("foreign : ".var_export($data, true));
            Logger::debug("getWorksheetByName : {$data[0]}");
            
            //var_dump($data[0]); die();
            $worksheet = $this->_getDocument()->getWorksheetByName($data[0]);
            if (!$worksheet) {
                $worksheet = $this->_getDocument()->getWorksheetByName($data[0]);
            }
            
            if (!$worksheet) throw new Exception("replaceVarsCallback: cant get worksheet {$data[0]}");
            
            return $worksheet->getValueByName($data[1]);
        }
        

        // BAD hack!!!!!
        if (!method_exists($this->_getWorksheet(), 'getValueByName')) {
            if (is_a($this->_worksheet, 'ExcelDocument')) {
                $this->_worksheet = $this->_worksheet->getActiveWorksheet();
            }
            //var_dump($this->_worksheet);die();
            //var_dump($this->_getWorksheet()); die();
        }
        
        return $this->_getWorksheet()->getValueByName($varName);
        //Logger::debug("str : ".var_export($str, true));
        //Logger::debug("callback vars : ".var_export(ExcelDocumentController::$vars, true));
        
        if (isset($this->_vars[$str[1]]))
            return $this->_vars[$str[1]];
        return '66';
    }
    
    public function replaceVars($formula, $vars=false ) {
        Logger::debug("_replaceVars2 $formula  ");
        $this->_vars = $vars;
        
        return preg_replace_callback("/(\w*\!*\w+\d+)/u", 'self::replaceVarsCallback', $formula);
    }
    
    public function parse($formula) {
        if (is_numeric($formula)) return $formula;
        
        Logger::debug("parse formula : $formula");
        $formula = str_replace('sum', 'SUM', $formula);
        $formula = str_replace('сумм', 'SUM', $formula);
        $formula = str_replace('СУММ', 'SUM', $formula);
        $formula = str_replace('среднее', 'AVERAGE', $formula);
        $formula = str_replace('СРЕДНЕЕ', 'AVERAGE', $formula);
        $formula = str_replace('СРЗНАЧ', 'AVERAGE', $formula);
        $formula = str_replace('срзнач', 'AVERAGE', $formula);
        //$formula = strtoupper($formula);
        
        $formula = preg_replace_callback("/([A-Z]+)\(([A-Za-z0-9\:\;]+)\)/u", 'self::callback', $formula);
        
        //$vars = $this->explodeFormulaVars($formula);
        /*$newVars = array();
        foreach($vars as $varName) {
            $newVars[$varName] = ExcelFactory::getDocument()->getActiveWorksheet()->getValueByName($varName);
        }*/
        $formula = $this->replaceVars($formula);
        Logger::debug("after replace vars : $formula");
        
        // теперь надо обработать выражение
        $a = null;
        $expr = '$a'.$formula.';';
        @eval($expr);
        if (is_null($a)) return null;
        return $a;
    }
    
    public function hasLinkVar($expr) {
        return preg_match_all("/([A-Za-А-Яа-я]+\!\w+\d+)/", $expr, $matches); 
    }
    
    public function validate($formula) {
        // если начало не с = значит это не формула
        if ($formula[0] != '=') return array('result'=>true);
        
        // проверим а не строка ли это у нас
        if (!preg_match_all("/\d+/u", $formula, $matches)) {
            return array('result'=>false, 'message'=>'Формула введена неправильно. Повторите ввод');
        }
        
        
        $vars = $this->explodeFormulaVars($formula);
        foreach($vars as $var) {
            Logger::debug("validate var : $var");
            $value = $this->_getWorksheet()->getValueByName($var);
            if (is_null($value)) return array('result'=>false, 'message'=>'Формула введена неправильно. Повторите ввод');
            
            if ($value=='')  return array('result'=>true, 'value'=>0);
            
            Logger::debug("value : $value");
            if (!is_numeric($value)) return array(
                'result' => false,
                'message' => "В ячейке $var введено текстовое значение. Повторите ввод"
            ); 

        }
        return array('result'=>true);
    }
    
    /**
     * Сдвигает переменные в формуле
     * @param string $formula формула, которую надо обработать 
     * @param string $column
     * @param int $string
     */
    public function shiftVars($formula, $column, $string, $range, ExcelWorksheet $worksheet) {
        
        $vars = $this->explodeFormulaVars($formula);
        
        // смещение по строке
        $stringShift = $string - $range['stringFrom'];
        
        // смещение по столбцу
        $columnIndex = $worksheet->getColumnIndex($column);
        $columnShift = $columnIndex - $range['columnFromIndex'];
        
        
        Logger::debug('vars :'.var_export($vars, true));
        if (count($vars)==0) return $formula; // нечего сдвигать
        
        
        
        $newVars = array();
        foreach($vars as $index=>$var) {
            $varInfo = $worksheet->explodeCellName($var); 
            $curColumn = $varInfo['column'];
            $curString = $varInfo['string'];
            $curColumnIndex = $worksheet->getColumnIndex($curColumn); 
            
            $curColumnIndex = $curColumnIndex + $columnShift;
            
            $curColumn = $worksheet->getColumnNameByIndex($curColumnIndex); 
            
            $curString = $curString + $stringShift;
            $newVars[$var] = $curColumn.$curString;
        }
        
        //Logger::debug('new vars :'.var_export($newVars, true));
        //Logger::debug("replace vars in formula : $formula");
        return $this->replaceVars($formula, $newVars);
    }
}

?>
