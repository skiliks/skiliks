<?php

// Team 'Develop - test.skiliks.com'
$sentryDsn = 'https://2ad3ec315fd04954a1e57102d0da8748:2e328e2ebfc34e289d74df3cdc0cf3b6@app.getsentry.com/15802';

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
    ),
    'params'=>array(
        'server_name'        => 'http://test.skiliks.com/', // формат 'http://domain.com/'
        'server_domain_name' => 'test.skiliks.com', // формат 'domain.com'
        'frontendUrl' => 'http://test.skiliks.com/',
        'sentry' => [
            'dsn' => $sentryDsn,
        ],
        'public' => [
            'isSkipOsCheck'        => true, //Проверка ОС перед стартом игры
            'isSkipBrowserCheck'   => true,
            'isDisplaySupportChat' => false,
        ]
    )
));


