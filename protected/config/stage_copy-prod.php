<?php

// Team 'Develop - live.skiliks.com'
$sentryDsn = 'https://41680afc32f344d88ab67eef43254684:e4265582b811477089af672d368c93bf@app.getsentry.com/15804';

define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks_copy_prod',
            'emulatePrepare' => true,
            'username' => 'user_copy_prod',
            'password' => 'AscbdTgs12-w',
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
        'server_name'        => 'http://copy-prod.skiliks.com/',
        'server_domain_name' => 'copy-prod.skiliks.com',
        'frontendUrl'        => 'http://copy-prod.skiliks.com/',
        'isBlockGhostLogin'  => false,
        'isUseStrictRulesForGhostLogin'=>false,
        'runMigrationOn' => 'live',
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


