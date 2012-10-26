<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExcelRange
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelRange {
    
    public static function parse($range) {
        //Logger::debug("_parseRange : $range");
        $res = preg_match_all("/(\w)(\d+)\:(\w)(\d+)/", $range, $matches); 
        //Logger::debug("matches : ".var_export($matches, true));
        if (!isset($matches[1][0])) return false;

        $columnFrom = $matches[1][0];
        $stringFrom = (int)$matches[2][0];
        
        $columnTo = $matches[3][0];
        $stringTo = (int)$matches[4][0];
        
        //Logger::debug("ws id : ".ExcelFactory::getDocument()->getActiveWorksheet()->id);
        //Logger::debug("get startIndex : $columnFrom");
        $startIndex = ExcelFactory::getDocument()->getActiveWorksheet()->getColumnIndex($columnFrom); // индекс колонки, с которой стартуем
        //Logger::debug("res : $startIndex");
        $endIndex = ExcelFactory::getDocument()->getActiveWorksheet()->getColumnIndex($columnTo); // индекс колонки, которой финишируем
        
        
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
    
    public static function toArray($range) {
        $rangeInfo = self::parse($range);
        //Logger::debug("rangeInfo : ".var_export($rangeInfo, true));
        
        $list = array();
        $columnTo = $rangeInfo['columnFromIndex'] + $rangeInfo['columnCount'];
        $stringTo = $rangeInfo['stringFrom'] + $rangeInfo['stringCount'];
        // бежим по колонкам
        for($columnIndex = $rangeInfo['columnFromIndex']; $columnIndex < $columnTo; $columnIndex++) {
            //Logger::debug("getColumnNameByIndex : $columnIndex");
            $columnName = ExcelFactory::getDocument()->getActiveWorksheet()->getColumnNameByIndex($columnIndex);
            //Logger::debug("found column : ".var_export($columnName, true));
            for($stringIndex = $rangeInfo['stringFrom']; $stringIndex < $stringTo; $stringIndex++) {
                $list[] = $columnName.$stringIndex;
                
                /*$value = ExcelFactory::getDocument()->getActiveWorksheet()->getValueByName($columnName.$stringIndex); 
                if ($value != '') $list[] = $value;*/
            }
        }
        
        return $list;
    }
}

?>
