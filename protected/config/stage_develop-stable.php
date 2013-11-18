<?php

// Team 'Develop - develop-stable.skiliks.com'
$sentryDsn = 'https://60f862bd0ea74119b99824cfdbb19ae6:e269c3c79d0242239a197e1acf8a9755@app.getsentry.com/15808';

define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks_develop_stable',
            'emulatePrepare' => true,
            'username' => 'dev_stable',
            'password' => 'AscbdTgs12-w',
            'charset' => 'utf8',

            'enableParamLogging'=>true,
            'enableProfiling'=>true
        ),
        'RSentryException'=> array(
            'dsn'   => $sentryDsn,
            'class' => 'application.components..yii-sentry-log.RSentryComponent',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'application.components.yii-sentry-log.RSentryLog',
                    'dsn'=> $sentryDsn,
                    'levels'=>'error, warning',
                ),
            ),
        ),
    ),
    'params'=>array(
        'server_name'    => 'http://skiliks.com/',
        'frontendUrl'    =>'http://live.skiliks.com/',
        'runMigrationOn' => 'live',
        'sentry' => [
            'dsn' => $sentryDsn,
        ],
        'public' => [
            'storageURL'           => 'http://storage.dev.skiliks.com/',
            'isLocalPc'            => true,
            'useSentryForJsLog'    => true,
            'isSkipBrowserCheck'   => true,
            'isDisplaySupportChat' => true,
        ]
    )
));


