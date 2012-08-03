<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExcelDrawing
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelDrawing {
    
    public static function apply($worksheetId, $column, $string, $target) {
        
            Logger::debug("actionDrawing");
            ExcelFactory::getDocument()->loadWorksheet($worksheetId);
            $worksheet = ExcelFactory::getDocument()->getWorksheet($worksheetId);
            
            Logger::debug("target : $target");
            $targetInfo = $worksheet->explodeCellName($target);
            Logger::debug("targetInfo : ".var_export($targetInfo, true));
            
            
            
            Logger::debug("get cell $column, $string");
            $cell = $worksheet->getCell($column, $string); 
            Logger::debug("cell : ".var_export($cell, true));
            if (!$cell) throw new Exception('cant find cell');
            if ($cell['formula'] == '') throw new Exception('no formula to apply');
            
            $formula = $cell['formula'];
            
            $excelFormula = new ExcelFormula();
            
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
                    $vars = $excelFormula->explodeFormulaVars($formula);
                    Logger::debug("vars : ".var_export($vars, true));
                    
                    $newFormula = $formula;
                    $newVars = array();
                    foreach($vars as $varName) {
                        $cellInfo = $worksheet->explodeCellName($varName);
                        $cellString = (int)$cellInfo['string'];
                        $cellString+=$step;
                        //$cellInfo['string'] = $i;
                        
                        $newVars[$varName] = $cellInfo['column'].$cellString;
                        //$newFormula = str_replace($varName, $cellInfo['column'].$cellString, $newFormula);
                    }
                    $step++;
                    Logger::debug("new vars : ".var_export($newVars, true));
                    $newFormula = $excelFormula->replaceVars($formula, $newVars);
                    
                    
                    $value = $excelFormula->parse($newFormula);
                    
                    
                    $curCell = $worksheet->getCell($column, $i);
                    $curCell['value'] = $value;
                    $curCell['formula'] = $newFormula;
                    $worksheet->replaceCell($curCell);
                    $worksheet->updateCellDb($curCell);
                    
                    
                    /*Logger::debug("get cell $column, $i");
                    $cell = $this->_getCell($column, $i);
                    $cell['value'] = $value;
                    $cell['formula'] = $newFormula;
                    
                    // изменим ячеку
                    $this->_setCell($column, $i, $cell);
                    $this->_updateCell($cell);*/
                    
                    
                    $result['worksheetData'][] = $curCell;
                }
            }
            else {
                Logger::debug("horizontal drawing");
                // горизонтальное протягивание
                $columnFromIndex = $worksheet->getColumnIndex($column)+1;
                $columnToIndex = $worksheet->getColumnIndex($targetInfo['column']);
                
                
                // выбираем переменные из формулы
                
                $vars = $excelFormula->explodeFormulaVars($formula);
                
                // бежим по колонкам
                $inc = 1;
                for($i = $columnFromIndex; $i<=$columnToIndex; $i++) {
                    
                    Logger::debug("process column $i");
                    //$vars = $this->_explodeFormulaVars($formula);
                    //$newFormula = $formula;
                    $newVars = array();
                    foreach($vars as $varName) {
                        $cellInfo = $worksheet->explodeCellName($varName);
                        
                        // сдвигаем колонку вправо
                        $curColumnIndex = $worksheet->getColumnIndex($cellInfo['column']);
                        $curColumnIndex = $curColumnIndex + $inc;
                        $cellInfo['column'] = $worksheet->getColumnNameByIndex($curColumnIndex);
                        
                        $newVars[$varName] = $cellInfo['column'].$cellInfo['string'];
                    }
                    Logger::debug("new vars : ".var_export($newVars, true));
                    
                    Logger::debug("before replace : $formula");
                    $newFormula = $excelFormula->replaceVars($formula, $newVars);
                    
                    
                    Logger::debug("new formula = $newFormula");
                    
                    Logger::debug("before _parseFormula $newFormula");
                    $value = $excelFormula->parse($newFormula);
                    //$value = $newFormula;
                    
                    Logger::debug("value = $value");
                    $column = $worksheet->getColumnNameByIndex($i);
                    
                    
                    
                    $curCell = $worksheet->getCell($column, $string);
                    $curCell['value'] = $value;
                    $curCell['formula'] = $newFormula;
                    $worksheet->replaceCell($curCell);
                    $worksheet->updateCellDb($curCell);
                    
                    
                    
                    /*$cell = $this->_getCell($column, $string);
                    $cell['value'] = $value;
                    $cell['formula'] = $newFormula;
                    
                    // изменим ячеку
                    $this->_setCell($column, $string, $cell);
                    $this->_updateCell($cell);*/
                    
                    $result['worksheetData'][] = $curCell;
                    
                    $inc++;
                }
            }
            
            $worksheet->saveToCache();

            return $result;
        
        
    }
}

?>
