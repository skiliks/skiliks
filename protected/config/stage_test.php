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
        'zoho' => array(
            'apiKey'              => 'b5e3f7316085c8ece12832f533c751be',
            'saveUrl'             => 'http://test.skiliks.com/zoho/saveExcel',
            'xlsTemplatesDirPath' => 'documents/templates',
            'templatesDirPath'    => 'documents/zoho',
            //'sendFileUrl'         => 'https://presheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
            'sendFileUrl'         => 'http://zoho.skiliks.com/remotedoc.im?apikey=%s&output=editor',
            'extExcel'            => 'xls'

        ),
    )
));


