<?php


/**
 * Description of ExcelPaire
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelPaire {
    
    public static function parse($expr) {
        if (!strstr($expr, ';')) return false;
        
        $data = explode(';', $expr);
        if (count($data) == 0) return false;
        
        return $data;
    }
}

?>
