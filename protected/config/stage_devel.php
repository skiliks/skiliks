<?php
return CMap::mergeArray(
    require(dirname(__FILE__) . '/base.php'),
    array(
        'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',

            'enableParamLogging' => true,
            'enableProfiling' => true
        ),
        'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    array(
                        'class' => 'CFileLogRoute',
                        'levels' => 'error, warning, info',
                    )

                ),
        ),
        'request' => array(
            'baseUrl' => '',
        ),
    ),
    'params' => [
        'assetsDebug' => true
    ]
    )
);
