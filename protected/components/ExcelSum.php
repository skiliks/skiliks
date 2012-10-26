<?php



/**
 * Description of ExcelSum
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelSum {
    
    public static function apply($expr) {
        // parse pair
        $cells = ExcelPaire::parse($expr);
        if (!$cells) {
            $cells = ExcelRange::toArray($expr);
            if (!$cells) return $expr; // нечего парсить
        }
        //Logger::debug("range ".var_export($cells, true));
        $values = array();
        foreach($cells as $cellName) {
            $value = ExcelFactory::getDocument()->getActiveWorksheet()->getValueByName($cellName);
            if ($value != '') $values[] = $value;
        }
        
        return array_sum($values); 
    }
}

?>
