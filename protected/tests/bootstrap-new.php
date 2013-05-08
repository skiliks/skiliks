<?php
define('YII_DEBUG', true);
$config = __DIR__ . '/../config/new_server.php';
require_once(__DIR__ . '/../../framework/yiit.php');
Yii::createWebApplication($config);
Yii::import('application.tests.base.*');