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
        'frontendUrl'=>'http://live.skiliks.com/',
        'public' => [
            'storageURL' => 'http://storage.dev.skiliks.com/v1',
            'isLocalPc'  => true,
        ],
        'zoho' => array(
            'apiKey'              => '2fa19dce9a7d7b74d97038208d6574b1',
            'saveUrl'             => 'http://live.skiliks.com/zoho/saveExcel',
            'xlsTemplatesDirPath' => 'documents/templates',
            'templatesDirPath'    => 'documents/zoho',
            //'sendFileUrl'         => 'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
            'sendFileUrl'         => 'http://zoho.skiliks.com/remotedoc.im?apikey=%s&output=editor',
            'extExcel'            => 'xls'
        ),

    )
));


