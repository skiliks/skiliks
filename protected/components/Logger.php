<?php



/**
 * Механизм логирования
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Logger {
    
    /**
     * Логирует строку
     * 
     * @param string $str что логируем
     * @param string $fileName куда логируем 
     */
    public static function debug($str, $fileName = 'logs/debug.log') {
        Yii::log($str, 'debug');
    }
}

?>
