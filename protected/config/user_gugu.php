<?php
return CMap::mergeArray(
    require(dirname(__FILE__) . '/base.php'),
    array('components' => array(
        'db' => array(
            'connectionString' => 'mysql:unix_socket=/tmp/mysql.sock;dbname=skiliks',
            'emulatePrepare' => true,
            'username' => 'skiliks',
            'password' => 'skiliks123',
            'charset' => 'utf8',

            'enableParamLogging' => true,
            'enableProfiling' => true
        )
    )
    )
);