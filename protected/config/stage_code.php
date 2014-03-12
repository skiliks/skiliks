<?php

// Team 'Develop - live.skiliks.com'
$sentryDsn = 'https://51b6e7f12ea6466cb155a9d842a03474:d51a648eaf4b48c39b73143455e54f7c@app.getsentry.com/20804';

define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=db.skiliks.com;dbname=skiliks',
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
        'session' => array(
            'timeout'                => 60*5, // 5 минут
        ),
    ),
    'params'=>array(
        'isBlockGhostLogin'  => false,
        'server_name'        => 'http://code.skiliks.com/',
        'server_domain_name' => 'code.skiliks.com',
        'frontendUrl'        => 'http://code.skiliks.com/',
        'runMigrationOn'     => 'code',

        'isUseStrictRulesForGhostLogin' => false,

        'sentry' => [
            'dsn' => $sentryDsn,
        ],
        'public' => [
            'storageURL'           => 'http://storage-2.skiliks.com/',
            'useSentryForJsLog'    => true,
            'isDisplaySupportChat' => true,
        ]
    )
));


