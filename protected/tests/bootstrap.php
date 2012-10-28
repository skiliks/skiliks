<?php
$yiit=dirname(__FILE__) . '/../../framework/yiit.php';
$config=dirname(__FILE__).'/../config/main.php';
require_once($yiit);
Yii::createWebApplication($config);