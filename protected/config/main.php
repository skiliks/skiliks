<?php

//define('SKILIKS_SPEED_FACTOR', 8);

return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',

            'enableParamLogging' => true,
            'enableProfiling' => true
        )
    )
))

?>
