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
            // Team 'Develop - develop.skiliks.com'
            'dsn'=> 'https://fbf47764a7a2443896b67b3df2a2b430:41eee5b68bf04c0f8c4d2a25e321bdf6@app.getsentry.com/15806',
            'class' => 'application.components..yii-sentry-log.RSentryComponent',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    // Team 'Develop - develop.skiliks.com'
                    'class'=>'application.components.yii-sentry-log.RSentryLog',
                    'dsn'=> 'https://fbf47764a7a2443896b67b3df2a2b430:41eee5b68bf04c0f8c4d2a25e321bdf6@app.getsentry.com/15806',
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


