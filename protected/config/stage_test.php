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
        'frontendUrl'=>'http://test.skiliks.com/',
        'zoho' => array(
            'apiKey'              => 'e52059ce3aeff6dd2c71afb9499bdcf7',
            'saveUrl'             => 'http://test.skiliks.com/zoho/saveExcel',
            'xlsTemplatesDirPath' => 'documents/templates',
            'templatesDirPath'    => 'documents/zoho',
            'sendFileUrl'         => 'https://presheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
            'extExcel'            => 'xls'
        ),
        'public' => [
            'isLocalPc'            => true,
            'isUseZohoProxy'       => false,
            'isDisplaySupportChat' => false,
        ]
    )
));


