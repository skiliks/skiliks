<?php

// Team 'Production - skiliks.com'
$sentryDsn = 'https://45ab0baac6fa42cd9a605c26a72f6c2c:1a06feee2a7a4a6ba778a9b11f56d337@app.getsentry.com/15803';

define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
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
        'db' => array(
            'connectionString' => 'mysql:host=db1.skiliks.com;dbname=skiliks',
            'emulatePrepare' => true,
            'username' => 'skiliks',
            'password' => 'dep-vep-eb-up-a',
            'charset' => 'utf8',
            'persistent'=>true,
            'enableParamLogging' => true,
            'enableProfiling' => true
        ),
    ),
    'params' => array(
        'frontendUrl' => 'http://front.skiliks.com/',
        'runMigrationOn' => 'production',
        'sentry' => [
            'dsn' => $sentryDsn,
        ],
        'public' => [
            'isLocalPc'          => true,
            'useSentryForJsLog'  => true,
            'isSkipBrowserCheck' => false,
            'isIncludeGoogleAnalyticsJavaScript' => true,
        ],
        'robokassa' => [
            'url'            => 'https://auth.robokassa.ru/Merchant/Index.aspx',
            'MrchLogin'      => 'leah',
            'sMerchantPass1' => 'as24ED4rFdrG456Dsd0d9f7gjec3',
            'sMerchantPass2' => 'd89DCs6d9ft66ygfr8iexmv67werd'
        ]
    )
));


