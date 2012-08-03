<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExcelClipboard
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
        
        $columnIndex=0; $stringIndex = 0;
        for($j = $rangeInfo['columnFromIndex']; $j<$columnTo; $j++) {
            //Logger::debug("get column name index $j ws $fromWorksheetId");
            $columnName = ExcelFactory::getDocument()->getWorksheet($fromWorksheetId)->getColumnNameByIndex($j);
            
            $stringIndex = 0;
            for($i = $rangeInfo['stringFrom']; $i<$stringTo; $i++) {
                $clipboard[$columnIndex][$stringIndex] = ExcelFactory::getDocument()->getWorksheet($fromWorksheetId)->getCell($columnName, $i);
                $stringIndex++;
            }
            
            $columnIndex++;
        }
        
        // Возврат результата
        $columnIndex = ExcelFactory::getDocument()->getWorksheet($toWorksheetId)->getColumnIndex($column);
        
        $columnTo = $columnIndex + $rangeInfo['columnCount']; 
        $stringTo = $string + $rangeInfo['stringCount'];
        
        
        $result = array();
        $result['result'] = 1;
        $result['worksheetData'] = array();
        
        Logger::debug('clipboard : '.var_export($clipboard, true));
        
        $excelFormula = new ExcelFormula();
        $stringIndex = $string;
        for($j=0; $j<$rangeInfo['columnCount'];$j++) {
            
            $columnName = ExcelFactory::getDocument()->getWorksheet($toWorksheetId)->getColumnNameByIndex($columnIndex);
            
            $stringIndex = $string;
            for($i = 0; $i<$rangeInfo['stringCount']; $i++) {
                $cell = $clipboard[$j][$i];
                
                // обработать формулу
                if ($cell['formula']!='') {
                    $cell['formula'] = $excelFormula->shiftVars($cell['formula'], $column, $string, $rangeInfo,
                            ExcelFactory::getDocument()->getWorksheet($toWorksheetId));
                    
                    Logger::debug("formula after shifting : ".$cell['formula']);
                    // пересчитаем формулу
                    $cell['value'] = $excelFormula->parse($cell['formula']);
                    if ($cell['value'] == '') $cell['value'] = $cell['formula'];
                }
                
                
                $cell['column'] = $columnName;
                $cell['string'] = $stringIndex;
                
                $result['worksheetData'][] = $cell;
                
                // запомним результат
                $curCell = ExcelFactory::getDocument()->getWorksheet($toWorksheetId)->getCell($columnName, $stringIndex);
                $curCell['value'] = $cell['value'];
                $curCell['formula'] = $cell['formula'];
                ExcelFactory::getDocument()->getWorksheet($toWorksheetId)->replaceCell($curCell);
                ExcelFactory::getDocument()->getWorksheet($toWorksheetId)->updateCellDb($curCell);
                ExcelFactory::getDocument()->getWorksheet($toWorksheetId)->saveToCache();
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
