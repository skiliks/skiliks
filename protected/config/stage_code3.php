<?php

// Team 'Develop - live.skiliks.com'
$sentryDsn = 'https://41680afc32f344d88ab67eef43254684:e4265582b811477089af672d368c93bf@app.getsentry.com/15804';

define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=db2.skiliks.com;dbname=skiliks',
            'emulatePrepare' => true,
            'username' => 'skiliks',
            'password' => 'scr-2sc-c5sncjs-asdmc',
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
                array(
                    // Team 'Develop - live.skiliks.com'
                    'class'  => 'application.components.yii-sentry-log.RSentryLog',
                    'dsn'    => $sentryDsn,
                    'levels' => 'error, warning',
                ),
            ),
        ),
    ),
    'params'=>array(
        'isBlockGhostLogin'  => false,
        'server_name'        => 'http://code2.skiliks.com/',
        'server_domain_name' => 'code2.skiliks.com',
        'frontendUrl'        => 'http://code2.skiliks.com/',
        'runMigrationOn'     => 'code2',

        'isUseStrictRulesForGhostLogin' => false,

        'sentry' => [
            'dsn' => $sentryDsn,
        ],
        'public' => [
            'storageURL'           => 'http://storage.dev.skiliks.com/',
            'useSentryForJsLog'    => true,
            'isDisplaySupportChat' => true,
        ]
    )
));


