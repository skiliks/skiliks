<?php
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
            'dsn'=> 'https://bfd7395024f24728afdf79e9034bca04:2f8bec2e2c40493dbf7b07db88afc94f@app.getsentry.com/4572',
            'class' => 'application.components..yii-sentry-log.RSentryComponent',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'application.components.yii-sentry-log.RSentryLog',
                    'dsn'=> 'https://bfd7395024f24728afdf79e9034bca04:2f8bec2e2c40493dbf7b07db88afc94f@app.getsentry.com/4572',
                    'levels'=>'error, warning',
                ),
            ),
        ),
    ),
    'params'=>array(
        'server_name'                   => 'http://skiliks.com/',
        'frontendUrl'=>'http://live.skiliks.com/',
        'runMigrationOn' => 'live',
        'public' => [
            'storageURL'           => 'http://storage.dev.skiliks.com/',
            'isLocalPc'            => true,
            'useSentryForJsLog'    => true,
            'isSkipBrowserCheck'   => true,
            'isDisplaySupportChat' => true,
        ]
    )
));


