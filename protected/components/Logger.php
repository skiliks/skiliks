<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Logger
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Logger {
    
    public static function debug($str) {
        if (!is_dir('logs')) {
            mkdir('logs', 0775);
        }

        $str = '['.date("d.m.Y H:i").'] ip: '.$_SERVER['REMOTE_ADDR'].' '.$str;
        $f = fopen('logs/debug.log', 'a+');
        fwrite($f, $str."\n");
        fclose($f);
    }
}

?>
