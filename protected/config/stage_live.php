<?php
define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks_live',
            'emulatePrepare' => true,
            'username' => 'skiliks_live',
            'password' => 'dep-vep-eb-up-a',
            'charset' => 'utf8',

            'enableParamLogging'=>true,
            'enableProfiling'=>true
        ),
        'RSentryException'=> array(
            // Team 'Develop - live.skiliks.com'
            'dsn'=> 'https://41680afc32f344d88ab67eef43254684:e4265582b811477089af672d368c93bf@app.getsentry.com/15804',
            'class' => 'application.components..yii-sentry-log.RSentryComponent',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    // Team 'Develop - live.skiliks.com'
                    'class'=>'application.components.yii-sentry-log.RSentryLog',
                    'dsn'=> 'https://41680afc32f344d88ab67eef43254684:e4265582b811477089af672d368c93bf@app.getsentry.com/15804',
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
            //'isSkipBrowserCheck'   => true,
            'isDisplaySupportChat' => true,
        ]
    )
));


