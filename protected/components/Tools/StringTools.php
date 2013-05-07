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
}

