<?php



/**
 * Description of Strings
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Strings {
    
    public static function toUtf8($str) {
        return iconv("Windows-1251", "UTF-8", $str);
    }
    
    public static function formatThousend($number, $showDecimals=false) {
        if ($showDecimals) $decimals = 2; else $decimals = 0;
        return number_format( $number,  $decimals, '.', ' ' );
    }
}

?>
