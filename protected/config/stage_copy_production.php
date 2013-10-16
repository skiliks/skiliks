<?php
define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks_copy_production',
            'emulatePrepare' => true,
            'username' => 'copy_production',
            'password' => 'AscbdTgs12-w',
            'charset'  => 'utf8',

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
        'frontendUrl'       => 'http://live.skiliks.com/',
        'disableOldLogging' => true,
        'runMigrationOn'    => 'production', //production - skiliks.com, live - live.skiliks.com, loc - loc.skiliks.com
        'public' => [
            'storageURL'        => 'http://skiliks.com/v1',
            'isLocalPc'         => true,
            'isUseZohoProxy'    => false,
            'useSentryForJsLog' => true,
        ],
        'zoho' => array(
            'apiKey'              => 'e52059ce3aeff6dd2c71afb9499bdcf7',
            'saveUrl'             => 'http://live.skiliks.com/zoho/saveExcel',
            'xlsTemplatesDirPath' => 'documents/templates',
            'templatesDirPath'    => 'documents/zoho',
            'sendFileUrl'         => 'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
            //'sendFileUrl'         => 'http://zoho.skiliks.com/remotedoc.im?apikey=%s&output=editor',
            'extExcel'            => 'xls'
        ),

    )
));


