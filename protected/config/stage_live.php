<?php

$sentryDsn = 'https://41680afc32f344d88ab67eef43254684:e4265582b811477089af672d368c93bf@app.getsentry.com/15804';

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
            // Team 'Develop - live.skiliks.com'
            'dsn'   => $sentryDsn,
            'class' => 'application.components..yii-sentry-log.RSentryComponent',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
//                array(
//                    'class'     => 'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
//                    'ipFilters' => array('127.0.0.1', '176.36.151.66'),
//                ),
                array(
                    // Team 'Develop - live.skiliks.com'
                    'class'  => 'application.components.yii-sentry-log.RSentryLog',
                    'dsn'    => $sentryDsn,
                    'levels' => 'error, warning',
                ),
            ),
        ),
        'session' => array(
            'timeout'                => 60*60*24*7, // 7дней
        ),
    ),
    'params'=>array(
        'isBlockGhostLogin'  => false,
        'server_name'        => 'http://live.skiliks.com/',
        'server_domain_name' => 'live.skiliks.com',
        'frontendUrl'        => 'http://live.skiliks.com/',
        'runMigrationOn'     => 'live',

        'isUseStrictRulesForGhostLogin' => false,

        'sentry' => [
            'dsn' => $sentryDsn,
        ],
        'public' => [
            'storageURL'           => 'http://storage.dev.skiliks.com/',
            'useSentryForJsLog'    => true,
            'isDisplaySupportChat' => true,
            'isSkipOsCheck'        => true,
        ]
    )
));


