<?php
// ���� ������������� Yii
require_once(dirname(__FILE__).'/framework/yii.php');

$config=dirname(__FILE__).'/protected/config/main.php';

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

    Yii::createWebApplication($config)->run();



?>