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
}

?>
