<?php

// Team 'Slavka - loc.skiliks.com'
$sentryDsn = 'https://fe6ceee54bac45aab0d99e90073265bd:259b382314bb47338329d6abefbb4995@app.getsentry.com/15801';

return CMap::mergeArray(
    require(dirname(__FILE__) . '/base.php'),
    array(
        'modules' => [
            'gii'=>array(
                'class'    => 'system.gii.GiiModule',
                'password' => 'MapRofi531',
            ),
        ],
        'components' => array(
            'db' => array(
                'connectionString' => 'mysql:host=127.0.0.1;dbname=skiliks_1', //
                'emulatePrepare' => true,
                'username' => 'root',
                'password' => '111',
                'charset' => 'utf8',
                'enableParamLogging' => true,
                'enableProfiling' => true
            ),
            'RSentryException'=> array(

                'dsn'   => $sentryDsn,
                'class' => 'application.components..yii-sentry-log.RSentryComponent',
            ),
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class'  => 'application.components.yii-sentry-log.RSentryLog',
                        'dsn'    => $sentryDsn,
                        'levels' => 'error, warning',
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
            'runMigrationOn'        => 'production',
            'sentry' => [
                'dsn' => $sentryDsn,
            ],
            'public' => [
                'useSentryForJsLog'    => true,
            ]
        )
    )
);