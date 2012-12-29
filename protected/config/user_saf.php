<?php
return CMap::mergeArray(
    require(dirname(__FILE__) . '/base.php'),
    array('components' => array(
        'db' => array(
            'connectionString' => 'mysql:dbname=skiliks',
            'emulatePrepare' => true,
            'username' => 'skiliks',
            'password' => 'skiliks123',
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

            ),
        ),
    ),
        'params' => array(
            'frontendUrl' => 'http://skiliks.loc/',
            'public' => [
                'skiliksSpeedFactor' => 8
            ]
        )
    )
);