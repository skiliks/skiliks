<?php



/**
 * Модуль математических функций.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Math {
    public static function avg($numbers) {
        return array_sum($numbers)/count($numbers);
    }
    
    /**
     * Проверяет дробную часть числа - не длинее ли она 6 символов
     * @param float $a
     * @return bool
     */
    public static function isMore6SignsFloat($a) {
        if (preg_match_all("/^(\d+)\.(\d+)$/", $a, $matches)) {
            if (isset($matches[2][0])) {
                $signs = $matches[2][0];
                if (strlen($signs) > 6) {
                    return true;
                }
            }
        }
        return false;
    }
}


