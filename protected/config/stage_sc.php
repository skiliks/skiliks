<?php

// for SocialCalc

define(YII_DEBUG, false);
return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks_sc',
            'emulatePrepare' => true,
            'username' => 'skiliks_sc',
            'password' => 'dep-vep-eb-up-a',
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

