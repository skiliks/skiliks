<?php
return CMap::mergeArray(
    require(dirname(__FILE__) . '/base.php'),
    array('components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=skiliks_2',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '111',
            'charset' => 'utf8',

            'enableParamLogging' => true,
            'enableProfiling' => true
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info, trace, log, debug',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info, trace, log, debug',
                    'categories'=>'system.db.*',
                    'logFile' => 'sql.log'
                ),

            ),
        ),
        'request' => array(
            'baseUrl' => 'http://skiliks.loc',
        ),
    ),
        'params' => array(
            'frontendUrl' => 'http://skiliks.loc/',
            'assetsDebug' => true,
            'public' => [
                'skiliksSpeedFactor' => 8
            ]
        )
    )
);