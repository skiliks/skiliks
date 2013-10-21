<?php
define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
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
        ],
        'zoho' => array(
            'saveUrl'     => 'http://skiliks.com/zoho/saveExcel',
            'apiKey'      => '32498387c50f6db99096ec9e70e4ea2a',
            'sendFileUrl' => 'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
        ),
        ]
    )
));


