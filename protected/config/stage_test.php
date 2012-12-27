<?php

return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks_test',
            'emulatePrepare' => true,
            'username' => 'skiliks_test',
            'password' => 'dep-vep-eb-up-a',
            'charset' => 'utf8',
            
             'enableParamLogging'=>true,
            'enableProfiling'=>true
        ),
    ),
    'params'=>array(
        'frontendUrl'=>'http://test.skiliks.com/',
    )
));


