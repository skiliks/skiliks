<?php
return CMap::mergeArray(
    require(dirname(__FILE__) . '/base.php'),
    array('components' => array(
        'db' => array(
            'connectionString' => 'mysql:dbname=svetlana_skiliks',
            'emulatePrepare' => true,
            'username' => 'svetlana',
            'password' => '',
            'charset' => 'utf8',

            'enableParamLogging' => true,
            'enableProfiling' => true
        ),
        'request' => array(
            'baseUrl' => 'http://svetlana.dev.skiliks.com',
        ),
    ),
        'params' => array(
            'frontendUrl' => 'http://svetlana.dev.skiliks.com/',
            'skiliksSpeedFactor' => 8,
        'assetsDebug' => true
    ))
);
