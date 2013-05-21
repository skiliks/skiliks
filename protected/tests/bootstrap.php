<?php
define('YII_DEBUG', true);
$config = __DIR__ . '/../config/main.php';
require_once(__DIR__ . '/../../framework/yiit.php');
PHPUnit_Extensions_SeleniumTestCase::shareSession(true);
Yii::createWebApplication($config);
Yii::import('application.tests.base.*');
