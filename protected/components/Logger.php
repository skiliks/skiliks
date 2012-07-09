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
        if (!is_dir('logs')) mkdir('logs', 0775);

        $str = '['.date("d.m.Y H:i").'] ip: '.$_SERVER['REMOTE_ADDR'].' '.$str;
        $f = fopen($fileName, 'a+');
        fwrite($f, $str."\n");
        fclose($f);
    }
}

?>
