<?php

return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
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
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning, info, trace, log',
                ),

            ),
        )
    ),
    'params'=>array(
        'frontendUrl'=>'http://live.skiliks.com/',
    )
));


