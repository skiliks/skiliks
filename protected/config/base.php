<?php
/**
 * Base app config
 */
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', true);
defined('YII_ENABLE_EXCEPTION_HANDLER') or define('YII_ENABLE_EXCEPTION_HANDLER', true);
ini_set('date.timezone', 'Europe/Moscow');

return array(
    'import' => array(
        'application.models.*',
        'application.models.Flags.*',
        'application.models.Assessment.*',
        'application.models.PullOfOptions.*',
        'application.models.Exceptions.*',
        'application.models.Logs.*',
        'application.models.Simulation.*',
        'application.models.User.*',
        /*'application.models.Characters.*',
        'application.models.DayPlan.*',
        'application.models.Dialog.*',
        'application.models.Events.*',
        'application.models.Excel*',
        'application.models.FlagsRules.*',
        'application.models.Mail.*',
        'application.models.MyDocuments.*',
        */
        'application.components.*',
        'application.components.Zoho.*',
        'application.components.Tools.*',
        'application.components.Excel.*',
        'application.components.Email.*',
        'application.components.Assessment.*',
        'application.components.Import.*',
        'application.components.Base.*',
        'application.components.ApiMethods.*',
        /*'application.components.DayPlan.*',
        'application.components.Dialog.*',
        'application.components.Excel.*',
        'application.components.Mail.*',*/
        'application.extensions.*',
        'application.extensions.PHPExcel.*',
        'application.vendors.*',
    ),
    'modules'=>array(
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'in-es-an-oyp-em',
            // 'ipFilters'=>array(…список IP…),
            // 'newFileMode'=>0666,
            // 'newDirMode'=>0777,
        ),
    ),
    'components' => array(
        'preload'=> array('log', 'RSentryException'),
        'cache' => array(
            'class' => 'CDbCache',
            'connectionID' => 'db',
            'autoCreateCacheTable' => false
        ),
        'session' => array(
            'class' => 'CDbHttpSession',
            'autoCreateSessionTable' => false,
            'connectionID' => 'db',
            'sessionName' => 'sid'

        ),
        'user' => array(
            'allowAutoLogin' => true,
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,

            'rules' => array(

                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                'site' => 'site/site',
            ),
        ),
        'excel'=>array(
            'class' => 'application.extensions.PHPExcel',
        ),
        'viewRenderer'=>array(
            'class'=>'application.extensions.smarty.ESmartyViewRenderer',
            'fileExtension' => '.tpl',
        ),
    ),
    'basePath' => dirname(__FILE__) . '/..',


    'preload' => array('log'),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        'frontendUrl' => 'http://front.skiliks.loc/',
        // This part will be sent to JS
        'public' => [
            'skiliksSpeedFactor' => 8,
            'simulationStartTime' => '9:00',
            'simulationEndTime' => '18:00',
            'storageURL' => 'http://storage.skiliks.com/v1/'
        ],
        'assetsDebug' => false,
        'zoho' => array(
            'apiKey'              => 'b5e3f7316085c8ece12832f533c751be',
            'saveUrl'             => 'http://live.skiliks.com/api/index.php/zoho/saveExcel',
            'xlsTemplatesDirPath' => 'documents/templates',
            'templatesDirPath'    => 'documents/zoho',
            'sendFileUrl'         => 'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
        ),
        'analizer' => array(
             'excel' => array(
                 'consolidatedBudget' => array(
                     'worksheetNames' => array(
                        'consolidated' => 'Сводный',
                        'sales'        => 'Продажи',
                        'production'   => 'Производство',
                        'logistic'     => 'Логистика',
                        'other'        => 'Прочее',    
                     ),
                     'etalons' => array(
                         1 => 876264,
                         2 => 3303417,
                         3 => 0,
                         4 => 0,
                         5 => 0,
                         6 => 0,
                         7 => 0.597951,
                         8 => 1.547943,
                         9 => 0.676173,
                     )
                 )
             )
        ),
    ),
);


