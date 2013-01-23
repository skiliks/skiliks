<?php
return CMap::mergeArray(
    require(dirname(__FILE__) . '/base.php'),
    array('components' => array(
        'db' => array(
            'connectionString' => 'mysql:dbname=kirill_skiliks',
            'emulatePrepare' => true,
            'username' => 'kirill',
            'password' => '',
            'charset' => 'utf8',

            'enableParamLogging' => true,
            'enableProfiling' => true
        ),
        'request' => array(
            'baseUrl' => 'http://kirill.dev.skiliks.com',
        ),
    ),
        'params' => array(
            'frontendUrl' => 'http://kirill.dev.skiliks.com/',
            'skiliksSpeedFactor' => 8,
        'assetsDebug' => true
    ))
);
