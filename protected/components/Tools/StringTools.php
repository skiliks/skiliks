<?php

/**
 *
 * @author slavka
 */
class StringTools
{
    /* this code contains MacOS Unicode magic */
    public static $CyToEn = array(
        "А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d",
        "Е" => "e", "Ё" => "yo", "Ж" => "zh", "З" => "z", "И" => "i",
        "Й" => "j", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n",
        "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t",
        "У" => "u", "Ф" => "f", "Х" => "kh", "Ц" => "ts", "Ч" => "ch",
        "Ш" => "sh", "Щ" => "sch", "Ъ" => "", "Ы" => "y", "Ь" => "",
        "Э" => "e", "Ю" => "yu", "Я" => "ya", "а" => "a", "б" => "b",
        "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "yo",
        "ж" => "zh", "з" => "z", "й" => "j", "й" => "j", "к" => "k",
        "л" => "l", "м" => "m", "и" => "i", "н" => "n", "о" => "o", "п" => "p",
        "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f",
        "х" => "kh", "ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch",
        "ъ" => "", "ы" => "y", "ь" => "", "э" => "e", "ю" => "yu",
        "я" => "ya"
    );

    public static function CyToEn($string)
    {
        foreach (self::$CyToEn as $ruChar => $enChar) {
            $string = mb_eregi_replace($ruChar, $enChar, $string);

        }
        return $string;
    }

    /**
     * @param Exception $e
     */
    public static function logException($e)
    {
        Yii::log('***');
        Yii::log($e->getMessage());
        Yii::log($e->getTraceAsString());
    }

    public static function getMaxLength($max_length, $string) {

        if(strlen($string) <= $max_length){
            return $string;
        }else{
            return substr($string, 0, $max_length).'...';
        }
    }

    /**
     * LAST LETTER METHOD
     */

    private static function oneDigitLastLetter($number) {   // Function, if number has only one digit
        if($number==1)  {
            return 0; // last digit is 1 (fe "1 месяц")
        }
        elseif($number==2 || $number==3 || $number==4) {
            return 1; // last digit is 2,3,4 (fe "2 месяца")
        }
        else {
            return 2; // last digit is not 1,2,3,4 (fe "5 месяцев")
        }
    }

    private static function digitLastLetter($number) {  // Function, if number has more than one digit
        if($number>9 && $number<21) {
            return 2;
        }
        else {
            return (self::oneDigitLastLetter(substr($number, -1)));
        }
    }

    /**
     * Words array should be in format "Месяц, месяца, месяцев"
     */

    public static function lastLetter($number, $words_array) {
        if($number<10) {
            return $words_array[self::oneDigitLastLetter($number)];		 // for didgit number
        }
        else {
            return $words_array[self::digitLastLetter($number)];   // for multiple digits number
        }
    }

    /**
     * END OF LAST LETTER METHOD
     */

}