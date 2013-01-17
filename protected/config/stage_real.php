<?php

return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'components' => array(
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks',
            'emulatePrepare' => true,
            'username' => 'skiliks',
            'password' => 'dep-vep-eb-up-a',
            'charset' => 'utf8',

            'enableParamLogging' => true,
            'enableProfiling' => true
        ),
    ),
    'params' => array(
        'frontendUrl' => 'http://front.skiliks.com/',
        'public' => [
            'skiliksSpeedFactor' => 4,
            'simulationStartTime' => '9:45',
        ],
        'zoho' => array(
            'apiKey'              => 'b5e3f7316085c8ece12832f533c751be',
            'saveUrl'             => 'http://skiliks.com/api/index.php/zoho/saveExcel',
            'xlsTemplatesDirPath' => 'documents/templates',
            'templatesDirPath'    => 'documents/zoho',
            'sendFileUrl'         => 'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
        ),
    )
));


