<?php

return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => 'Vtufpfdh',
            'charset' => 'utf8',

            'enableParamLogging' => true,
            'enableProfiling' => true
        ),
    ),
    'params' => array(
        'frontendUrl' => 'http://front.skiliks.com/',
        'simulationStartTime' => '9:45',
        'skiliksSpeedFactor' => 4,
        'zoho' => array(
            'apiKey'              => 'b5e3f7316085c8ece12832f533c751be',
            'saveUrl'             => 'http://skiliks.com/api/index.php/zoho/saveExcel',
            'xlsTemplatesDirPath' => 'documents/excel',
            'templatesDirPath'    => 'documents',
            'sendFileUrl'         => 'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
        ),
    )
));


