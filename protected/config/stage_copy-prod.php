<?php

// Team 'Develop - copy-prod.skiliks.com'
$sentryDsn = 'https://952c65e5350746718e2d71a9cae64b10:7f82ba92be494d73aceea74cc2e3b1ff@app.getsentry.com/17167';

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


