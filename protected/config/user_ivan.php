<?php

$sentryDsn = 'https://45c713ca0e994aeea5aa7bef1850eeca:37efcc80c8434f40a99900c54fc30ac8@app.getsentry.com/15851';

return CMap::mergeArray(
    require(dirname(__FILE__) . '/base.php'),
    array(
        'modules' => [
            'gii'=>array(
                'class'    =>'system.gii.GiiModule',
                'password' =>'MapRofi531',
            ),
        ],
        'components' => array(
            'db' => array(
                'connectionString' => 'mysql:host=127.0.0.1;dbname=skiliks_develop',
                'emulatePrepare' => true,
                'username' => 'root',
                'password' => '1111',
                'charset' => 'utf8',

                'enableParamLogging' => true,
                'enableProfiling' => true
            ),
            'RSentryException'=> array(

                'dsn'   => $sentryDsn,
                'class' => 'application.components..yii-sentry-log.RSentryComponent',
            ),
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class'  => 'application.components.yii-sentry-log.RSentryLog',
                        'dsn'    => $sentryDsn,
                        'levels' => 'error, warning',
                    ),
                ),
            ),
            'request' => array(
                'baseUrl' => '',
            ),
        ),
        'params' => array(
            'isBlockGhostLogin' => false,
            'isUseStrictRulesForGhostLogin'=>false,
            'frontendUrl' => 'http://loc.skiliks.com/',
            'assetsDebug' => true,
            'sentry' => [
                'dsn' => $sentryDsn,
            ],
            'public' => [
                'useSentryForJsLog'  => true,
                'isSkipBrowserCheck' => true,
            ]
        )
    )
);