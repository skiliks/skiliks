<?php
define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks_test',
            'emulatePrepare' => true,
            'username' => 'skiliks_test',
            'password' => 'dep-vep-eb-up-a',
            'charset' => 'utf8',
            
            'enableParamLogging'=>true,
            'enableProfiling'=>true
        ),
        'RSentryException'=> array(
            // Team 'Develop - test.skiliks.com'
            'dsn'=> 'https://2ad3ec315fd04954a1e57102d0da8748:2e328e2ebfc34e289d74df3cdc0cf3b6@app.getsentry.com/15802',
            'class' => 'application.components..yii-sentry-log.RSentryComponent',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    // Team 'Develop - test.skiliks.com'
                    'class'=>'application.components.yii-sentry-log.RSentryLog',
                    'dsn'=> 'https://2ad3ec315fd04954a1e57102d0da8748:2e328e2ebfc34e289d74df3cdc0cf3b6@app.getsentry.com/15802',
                    'levels'=>'error, warning',
                ),
            ),
        ),
    ),
    'params'=>array(
        'server_name' => 'http://skiliks.com/',
        'frontendUrl'=>'http://test.skiliks.com/',
        'public' => [
            'isLocalPc'            => true,
            'isDisplaySupportChat' => false,
        ]
    )
));


