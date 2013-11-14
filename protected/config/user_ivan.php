<?php
return CMap::mergeArray(
    require(dirname(__FILE__) . '/base.php'),
    array(
        'modules' => [
            'gii'=>array(
                'class'    =>'system.gii.GiiModule',
                'password' =>'MapRofi531',
                //'ipFilters'=>array('62.205.135.161'),
                // 'newFileMode'=>0666,
                // 'newDirMode'=>0777,
            ),
        ],
        'components' => array(
            'db' => array(
                'connectionString' => 'mysql:host=127.0.0.1;dbname=skiliks_develop',
                'emulatePrepare' => true,
                'username' => 'root',
                'password' => '1111',
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
                'baseUrl' => '',
            ),
        ),
        'params' => array(
            'frontendUrl' => 'http://loc.skiliks.com/',
            'assetsDebug' => true,
            'isUseResultPopUpCache' => false,
            'public' => [
                'isLocalPc'          => true,
                'useSentryForJsLog'  => true,
                'isSkipBrowserCheck' => true,
            ]
        )
    )
);