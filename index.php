<?php
// ���� ������������� Yii

header("HTTP/1.0 200 OK");
header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header('Access-Control-Allow-Origin: *');

require_once(dirname(__FILE__).'/framework/yii.php');

$config=dirname(__FILE__).'/protected/config/main.php';

ini_set('date.timezone', 'Etc/GMT');
set_time_limit(300); 
/*
function WorkError($code, $msg, $file, $line){
  // Пишем лог
  $fh = fopen('err_logs/error.log', 'a');
  fputs($fh, '['.date("d.m.Y H:i").'] User '.$_SERVER['REMOTE_ADDR'].' generated error "'.$msg.'" in '.$file.' at line '.$line.' Code '.$code."\n");
  fclose($fh);
  die();
}

ni_set('display_errors',1);
error_reporting(E_ALL);

define('YII_ENABLE_ERROR_HANDLER', false); 
define('YII_ENABLE_EXCEPTION_HANDLER', false);
        

set_error_handler("WorkError");
*/

//define('YII_ENABLE_ERROR_HANDLER', true); 
//define('YII_ENABLE_EXCEPTION_HANDLER', true);

// ������� ��������� ���������� � ��������� ���
defined('YII_DEBUG') or define('YII_DEBUG',true);

    Yii::createWebApplication($config)->run();



?>