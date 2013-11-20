<?php
// Team 'Develop - master.skiliks.com'
$sentryDsn = 'https://12e4e56859fd4e09b30bf7f29caf0889:3a6ab5d02d4849d0a652c4d93e91d513@app.getsentry.com/15805';

define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks_master',
            'emulatePrepare' => true,
            'username' => 'skiliks_master',
            'password' => 'aw-s-erf-dt-2we',
            'charset' => 'utf8',

            'enableParamLogging'=>true,
            'enableProfiling'=>true
        ),
        'RSentryException'=> array(
            'dsn'=> $sentryDsn,
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
        'frontendUrl'=>'http://master.skiliks.com/',
        'public' => [],
        'sentry' => [
            'dsn' => $sentryDsn,
        ],
        'robokassa' => [
            'url'            => 'https://auth.robokassa.ru/Merchant/Index.aspx',
            'MrchLogin'      => 'leah',
            'sMerchantPass1' => 'as24ED4rFdrG456Dsd0d9f7gjec3',
            'sMerchantPass2' => 'd89DCs6d9ft66ygfr8iexmv67werd'
        ],
    )
));


