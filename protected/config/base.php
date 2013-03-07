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
        'application.models.Mail.*',
        'application.models.Activities.*',
        /*'application.models.Character.*',
        'application.models.DayPlan.*',
        'application.models.Replica.*',
        'application.models.Events.*',
        'application.models.Excel*',
        'application.models.FlagsRules.*',
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
        'application.components.GameContentAnalyze.*',
        /*'application.components.DayPlan.*',
        'application.components.Replica.*',
        'application.components.Excel.*',
        'application.components.Mail.*',*/
        'application.extensions.*',
        'application.extensions.PHPExcel.*',
        'application.vendors.*',

        'application.modules.user.models.*',
        'application.modules.avatar.models.*',
        //application.modules.friendship.models.*',
        //'application.modules.membership.models.*',
        'application.modules.message.models.*',
        'application.modules.profile.models.*',
        'application.modules.registration.models.*',
        'application.modules.role.models.*',
        'application.modules.usergroup.models.*',
    ),
    'modules'=>array(
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'in-es-an-oyp-em',
            // 'ipFilters'=>array(…список IP…),
            // 'newFileMode'=>0666,
            // 'newDirMode'=>0777,
        ),
        'user' => array(
            'debug' => true,
        ),
        'avatar'       => [],
        //'friendship'   => [],
        //'membership'   => [],
        'message'      => [],
        'profile'      => [],
        'registration' => [],
        'role'         => [],
        'usergroup'    => [],
    ),
    'sourceLanguage'    =>'en_US',
    'language'          =>'ru_RU',
    'components' => array(
        'preload'=> array('log', 'RSentryException'),
        'assetManager' => [
            'linkAssets' => true,
        ],
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
            'class' => 'application.modules.user.components.YumWebUser',
            'allowAutoLogin' => true,
            // 'loginUrl' => array('//user/user/login'),
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,

            'rules' => array(
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                'site' => 'site/site',
                'team' => 'page/team'
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
        'frontendUrl' => 'http://skiliks.loc/',
        // This part will be sent to JS
        'public' => [
            'skiliksSpeedFactor' => 8,
            'simulationStartTime' => '9:00',
            'simulationEndTime' => '18:00',
            'storageURL' => 'http://storage.skiliks.com/v1/'
        ],
        'zoho' => array(
            'apiKey'              => 'c076746cd578f7e9287ff1234d3faf2f',
            'saveUrl'             => 'http://live.skiliks.com/zoho/saveExcel',
            'xlsTemplatesDirPath' => 'documents/templates',
            'templatesDirPath'    => 'documents/zoho',
            'sendFileUrl'         => 'https://presheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
        ),
        'initial_data' => [
            'users' => [
                /* is_admin = 1 -- user will be admin */
                ['username' => 'gugu'    , 'email' => 'gugu@skiliks.com'    ,'password' => 'gfhjkm'         ,'is_admin' => 1],
                ['username' => 'slavka'  , 'email' => 'slavka@skiliks.com'  ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'asd'     , 'email' => 'asd@skiliks.com'     ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'vad'     , 'email' => 'vad@skiliks.com'     ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'listepo' , 'email' => 'ivan@skiliks.com'    ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'tony'    , 'email' => 'tony@skiliks.com'    ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'leah'    , 'email' => 'leah@skiliks.com'    ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'masha'   , 'email' => 'masha@skiliks.com'   ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'pernifin', 'email' => 'pernifin@skiliks.com' ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'kirill'  , 'email' => 'kirill@skiliks.com'  ,'password' => 'wu-wod-bo-slyub','is_admin' => 1],
                ['username' => 'tatiana' , 'email' => 'tatiana@skiliks.com' ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'ahmed'   , 'email' => 'ahmed@zoho.com'      ,'password' => 'zohozoho'       ,'is_admin' => 1]
            ]
        ],
        'test_mappings' => require(dirname(__FILE__) . '/test_mappings.php'),
        'analizer' => array(
             'excel' => array(
                 'consolidatedBudget' => array(
                     'worksheetNames' => array(
                        'consolidated' => 'Consolidated budget',
                        'sales'        => 'Sales',
                        'production'   => 'Production',
                        'logistic'     => 'Logistics',
                        'other'        => 'Misc',
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
             ),
            'emails' => [
                '3326' => [
                    'limitToGetPoints'  => 0.5,
                    'limitToGet1points' => 0.3,
                    'limitToGet2points' => 0.1,
                ]
            ]
        ),
    ),
);


