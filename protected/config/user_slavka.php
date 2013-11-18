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
                'connectionString' => 'mysql:host=127.0.0.1;dbname=skiliks_1', //
                //'connectionString' => 'mysql:host=test.skiliks.com;dbname=skiliks_test',
                // production_2 - live,
                // production - master
                'emulatePrepare' => true,
                'username' => 'root',
                'password' => '111',
                //'username' => 'live_tester',
                //'password' => 'MapRofi123',
                'charset' => 'utf8',

                'enableParamLogging' => true,
                'enableProfiling' => true
            ),
            'RSentryException'=> array(
                // Team 'Slavka - loc.skiliks.com'
                'dsn'=> 'https://fe6ceee54bac45aab0d99e90073265bd:259b382314bb47338329d6abefbb4995@app.getsentry.com/15801',
                'class' => 'application.components..yii-sentry-log.RSentryComponent',
            ),
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class'=>'application.components.yii-sentry-log.RSentryLog',
                        'dsn'=> 'https://fe6ceee54bac45aab0d99e90073265bd:259b382314bb47338329d6abefbb4995@app.getsentry.com/15801',
                        'levels'=>'error, warning',
                    ),
                ),
            ),
            //        'log' => array(
            //            'class' => 'CLogRouter',
            //            'routes' => array(
            //                array(
            //                    'class' => 'CFileLogRoute',
            //                    'levels' => 'error, warning, info, trace, log, debug',
            //                ),
            //                array(
            //                    'class' => 'CFileLogRoute',
            //                    'levels' => 'error, warning, info, trace, log, debug',
            //                    'categories'=>'system.db.*',
            //                    'logFile' => 'sql.log'
            //                ),
            //
            //            ),
            //        ),
            'request' => array(
                'baseUrl' => '',
            ),
        ),
        'params' => array(
            'frontendUrl' => 'http://skiliks.loc/',
            'assetsDebug' => true,
            'isUseResultPopUpCache' => false,
            'runMigrationOn'        => 'production',
            'public' => [
                'isLocalPc'            => true,
                'isUseZohoProxy'       => false,
                'useSentryForJsLog'    => true,
                'isSkipBrowserCheck'   => true,
                'isDisplaySupportChat' => false,
            ]
        )
    )
);