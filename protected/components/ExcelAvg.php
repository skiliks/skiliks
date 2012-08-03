<?php



/**
 * Description of ExcelAvg
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelAvg {
    
    public static function apply($expr) {
        // parse pair
        $cells = ExcelPaire::parse($expr);
        if (!$cells) {
            $cells = ExcelRange::toArray($expr);
            if (!$cells) return $expr; // нечего парсить
        }
        
        $values = array();
        foreach($cells as $cellName) {
            $value = ExcelFactory::getDocument()->getActiveWorksheet()->getValueByName($cellName);
            if ($value >0) $values[] = $value;
        }
        Logger::debug("avg values : ".var_export($values, true));
        if (count($values)>0) {
            return Math::avg($values);
        }
        
        return $expr; 
    }
}

?>
