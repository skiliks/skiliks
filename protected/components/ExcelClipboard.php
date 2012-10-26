<?php



/**
 * Клипбоард
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelClipboard {
    
    public static function paste($fromWorksheetId, $toWorksheetId, $column, $string, $range) {
        
        
        $rangeInfo = ExcelRange::parse($range);
        //$rangeInfo = $this->_parseRange($range);
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
        
        $fromWorksheet = new ExcelWorksheet();
        $fromWorksheet->load($fromWorksheetId);
        //ExcelFactory::getDocument()->getWorksheet($fromWorksheetId)
        
        $columnIndex=0; $stringIndex = 0;
        for($j = $rangeInfo['columnFromIndex']; $j<$columnTo; $j++) {
            //Logger::debug("get column name index $j ws $fromWorksheetId");
            $columnName = $fromWorksheet->getColumnNameByIndex($j);
            
            $stringIndex = 0;
            for($i = $rangeInfo['stringFrom']; $i<$stringTo; $i++) {
                $clipboard[$columnIndex][$stringIndex] = $fromWorksheet->getCell($columnName, $i);
                $stringIndex++;
            }
            
            $columnIndex++;
        }
        
        // Возврат результата
        $toWorksheet = new ExcelWorksheet();
        $toWorksheet->load($toWorksheetId);
        //ExcelFactory::getDocument()->getWorksheet($toWorksheetId)
        
        $columnIndex = $toWorksheet->getColumnIndex($column);
        
        $columnTo = $columnIndex + $rangeInfo['columnCount']; 
        $stringTo = $string + $rangeInfo['stringCount'];
        
        
        $result = array();
        $result['result'] = 1;
        $result['worksheetData'] = array();
        
        //Logger::debug('clipboard : '.var_export($clipboard, true));
        
        $excelFormula = new ExcelFormula();
        //$excelFormula->setDocument($document)
        $stringIndex = $string;
        for($j=0; $j<$rangeInfo['columnCount'];$j++) {
            
            $columnName = $toWorksheet->getColumnNameByIndex($columnIndex);
            
            $stringIndex = $string;
            for($i = 0; $i<$rangeInfo['stringCount']; $i++) {
                $cell = $clipboard[$j][$i];
                
                // обработать формулу
                if ($cell['formula']!='') {
                    $cell['formula'] = $excelFormula->shiftVars($cell['formula'], $column, $string, $rangeInfo,
                            $toWorksheet);
                    
                    //Logger::debug("formula after shifting : ".$cell['formula']);
                    // пересчитаем формулу
                    $cell['value'] = $excelFormula->parse($cell['formula']);
                    if ($cell['value'] == '') $cell['value'] = $cell['formula'];
                }
                
                
                $cell['column'] = $columnName;
                $cell['string'] = $stringIndex;
                
                $result['worksheetData'][] = $cell;
                
                // запомним результат
                $curCell = $toWorksheet->getCell($columnName, $stringIndex);
                $curCell['value'] = $cell['value'];
                $curCell['formula'] = $cell['formula'];
                $toWorksheet->replaceCell($curCell);
                $toWorksheet->updateCellDb($curCell);
                $toWorksheet->saveToCache();
                /*
                $params = array(
                    'worksheetId' => $worksheetId,
                    'column' => $columnName,
                    'string' => $stringIndex,
                    'value' => $cell['value'],
                    'formula' => $cell['formula']
                );
                $this->_updateCell($params);*/
                
                $stringIndex++;
            }
            $columnIndex++;
        }
        
        return $result;
    }
}

?>
