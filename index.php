update!
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
/**
 * Main page
 *
 * PHP Version 5.4
 *
 * @category PHP
 * @package  None
 * @author   Andrii Kostenko <andrey@skiliks.com>
 * @license  proprietary http://skiliks.com/
 * @link     skiliks.com
 */
header("HTTP/1.0 200 OK");
header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header('Access-Control-Allow-Origin: *');

defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once dirname(__FILE__).'/framework/yii.php';

$config=dirname(__FILE__).'/protected/config/main.php';

set_time_limit(600);
Yii::createWebApplication($config)->run();
