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

    public static function write($data) {
        if(is_object($data)){
            $data = json_encode($data);
        }
        $filename = __DIR__.'/../runtime/logger.log';
        file_put_contents($filename, $data, FILE_APPEND);
        file_put_contents($filename, "\r\n", FILE_APPEND);
    }

    public static function logMailCode($data) {
        if(!is_array($data)){ return; }

        $filename = __DIR__.'/../runtime/logger.log';
        if(!empty($data['events']) and is_array($data['events'])){
            foreach($data['events'] as $mail) {
                if($mail['eventType'] === 'M'){
                    file_put_contents($filename, $mail['id'], FILE_APPEND);
                    file_put_contents($filename, ",", FILE_APPEND);
                }

            }
        }

    }
}


