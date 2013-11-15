<?php
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
        'frontendUrl'=>'http://master.skiliks.com/',
        'public' => [],
        'robokassa' => [
            'url'            => 'https://auth.robokassa.ru/Merchant/Index.aspx',
            'MrchLogin'      => 'leah',
            'sMerchantPass1' => 'as24ED4rFdrG456Dsd0d9f7gjec3',
            'sMerchantPass2' => 'd89DCs6d9ft66ygfr8iexmv67werd'
        ],
    )
));


