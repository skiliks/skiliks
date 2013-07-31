<?php

// for SocialCalc

define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks_dev2',
            'emulatePrepare' => true,
            'username' => 'skiliks_dev2',
            'password' => 'AscbdTgs12-w',
            'charset' => 'utf8',

            'enableParamLogging'=>true,
            'enableProfiling'=>true
        )
    ),
    'params'=>array(
        'frontendUrl' => 'http://socialcalc.skiliks.com/',
        'zoho'        => [],
        'public' => [
            'useSentryForJsLog' => false,
        ],
    )
));

