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
        'application.models.Interfaces.*',

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
        'application.components.ForStaticSite.*',
        /*'application.components.DayPlan.*',
        'application.components.Replica.*',
        'application.components.Excel.*',
        'application.components.Mail.*',*/
        'application.extensions.*',
        'application.extensions.PHPExcel.*',
        'application.extensions.phpmailer.*',
        'application.vendors.*',

        'application.modules.user.models.*',
        'application.modules.user.components.*',
        'application.modules.avatar.models.*',
        //application.modules.friendship.models.*',
        //'application.modules.membership.models.*',
        'application.modules.message.models.*',
        'application.modules.profile.models.*',
        'application.modules.registration.models.*',
        'application.modules.user.controllers.*',
        'application.modules.role.models.*',
        'application.modules.usergroup.models.*',

        'application.controllers.static.*',
    ),
    'modules'=>array(
        'gii'=>array(
            'class'    =>'system.gii.GiiModule',
            'password' =>'in-es-an-oyp-em',
            // 'ipFilters'=>array(…список IP…),
            // 'newFileMode'=>0666,
            // 'newDirMode'=>0777,
        ),
        'user' => array(
            'debug' => false,
            'activationPasswordSet' => false,
            'mailer'=>'PHPMailer',
            'loginType' => 2,
            'returnLogoutUrl' => '/',
            'phpmailer'=>array(
                'transport'=>'smtp',
                'html'=>true,
                'properties'=>array(
                    'CharSet'    => 'UTF-8', // SMTP server
                    'SMTPDebug'  => false,          // enables SMTP debug information (for testing)
                    'SMTPAuth'   => true,            // enable SMTP authentication
                    'SMTPSecure' => 'tls',         // sets the prefix to the servier
                    'Host'       => 'smtp.yandex.ru',                   // set the SMTP port for the GMAIL server
                    'Username'   => 'support@skiliks.com',  // GMAIL username
                    'Password'   => 'skiliks531',        // GMAIL password
                ),
                'msgOptions'=>array(
                    'fromName'=>'Registration System',
                    'toName'=>'You doomed user',
                ),
            )
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
    'language'          =>'en_US',
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
                '/'               => 'static/pages/index',
                'static/'         => 'static/pages/index',

                'static/team/<_lang:\w+>'    => 'static/pages/team',
                'static/product/<_lang:\w+>' => 'static/pages/product',

                'static/team/'    => 'static/pages/team',
                'static/product/' => 'static/pages/product',

                'subscription/add'       => 'static/pages/addUserSubscription',
                'static/pages/addUserSubscription' => 'static/pages/addUserSubscription', // strange, but works in this way only

                'static/<_lang:\w+>'         => 'static/pages/index',

                'admin'                  => 'static/admin/',
                'admin/<action:\w+>'     => 'static/admin/<action>',
                'Admin/Log'              => 'static/Admin/Log',

                'logout'                  => 'static/userAuth/logout',
                'logout/<_lang:\w+>'                  => 'static/userAuth/logout',

                'simulation/start'        => 'simulation/start',
                'simulation/stop'         => 'simulation/stop',
                'simulation/changeTime'   => 'simulation/changeTime',
                'simulation/startPause'   => 'simulation/startPause',
                'simulation/stopPause'    => 'simulation/stopPause',

                'simulation/<mode:\w+>/<type:\w+>'   => 'static/site/simulation',

                'registration'                         => 'static/userAuth/registration',
                'registration/<_lang:\w+>'                         => 'static/userAuth/registration',

                'registration/choose-account-type'     => 'static/userAuth/chooseAccountType',
                'registration/choose-account-type/<_lang:\w+>'     => 'static/userAuth/chooseAccountType',

                'registration/account-type/added'        => 'static/userAuth/accountTypeSavesSuccessfully',
                'registration/account-type/added/<_lang:\w+>'        => 'static/userAuth/accountTypeSavesSuccessfully',

                'results'                   =>'static/userAuth/results',
                'results/<_lang:\w+>'                   =>'static/userAuth/results',
                'simulation/results'        =>'static/userAuth/results', // just to handle JS 'Sim stop'
                'simulation/results/<_lang:\w+>'        =>'static/userAuth/results', // just to handle JS 'Sim stop'
                'site/results'              =>'static/userAuth/results',
                'site/results/<_lang:\w+>'              =>'static/userAuth/results',

                'static/comingSoonSuccess' => 'static/pages/comingSoonSuccess',
                'static/comingSoonSuccess/<_lang:\w+>' => 'static/pages/comingSoonSuccess',

                'userAuth/<action:\w+>'    => 'static/userAuth/<action>',
                'registration/<action:\w+>'   => 'static/userAuth/<action>',

                'cheats'          => 'static/cheats/mainPage',
                'cheats/cleanUpAccount'          => 'static/cheats/cleanUpAccount',
                'cheats/setinvites/<status:\w+>' => 'static/cheats/setStatusForAllInvites',

                'dashboard/'          => 'static/dashboard/index',
                'dashboard/corporate' => 'static/dashboard/corporate',
                'dashboard/personal'  => 'static/dashboard/personal',

                'profile/'          => 'static/profile/index',
                'profile/corporate' => 'static/profile/corporate',
                'profile/personal'  => 'static/profile/personal',

                'statistic/'          => 'static/statistic/index',
                'statistic/corporate' => 'static/statistic/corporate',
                'statistic/personal'  => 'static/statistic/personal',

                'notifications/'          => 'static/notifications/index',
                'notifications/corporate' => 'static/notifications/corporate',
                'notifications/personal'  => 'static/notifications/personal',

                'simulations/'                 => 'static/simulations/index',
                'simulations/details/<id:\w+>' => 'static/simulations/details',
                'simulations/corporate'        => 'static/simulations/corporate',
                'simulations/personal'         => 'static/simulations/personal',

                'dashboard/<_lang:\w+>'          => 'static/dashboard/index',
                'dashboard/corporate/<_lang:\w+>' => 'static/dashboard/corporate',
                'dashboard/personal/<_lang:\w+>'  => 'static/dashboard/personal',

                'profile/<_lang:\w+>'          => 'static/profile/index',
                'profile/corporate/<_lang:\w+>' => 'static/profile/corporate',
                'profile/personal/<_lang:\w+>'  => 'static/profile/personal',

                'statistic/<_lang:\w+>'          => 'static/statistic/index',
                'statistic/corporate/<_lang:\w+>' => 'static/statistic/corporate',
                'statistic/personal/<_lang:\w+>'  => 'static/statistic/personal',

                'notifications/<_lang:\w+>'          => 'static/notifications/index',
                'notifications/corporate/<_lang:\w+>' => 'static/notifications/corporate',
                'notifications/personal/<_lang:\w+>'  => 'static/notifications/personal',

                'simulations/<_lang:\w+>'                 => 'static/simulations/index',
                'simulations/details/<id:\w+>/<_lang:\w+>' => 'static/simulations/details',
                'simulations/corporate/<_lang:\w+>'        => 'static/simulations/corporate',
                'simulations/persona/<_lang:\w+>l'         => 'static/simulations/personal',

                'dashboard/invite/remove/<inviteId:\w+>' => 'static/dashboard/removeInvite',
                'dashboard/invite/resend/<inviteId:\w+>' => 'static/dashboard/reSendInvite',

                'dashboard/invite/remove/<inviteId:\w+>/<_lang:\w+>' => 'static/dashboard/removeInvite',
                'dashboard/invite/resend/<inviteId:\w+>/<_lang:\w+>' => 'static/dashboard/reSendInvite',

                'invite/add-10'                => 'static/invites/increaseInvites',

                'dashboard/send-invite'               => 'static/dashboard/sendInviteEmail',
                'dashboard/accept-invite/<code:\w+>'  => 'static/dashboard/acceptInvite',
                'dashboard/decline-invite/<code:\w+>' => 'static/dashboard/declineInvite',

                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',

                '<controller:\w+>/<id:\d+>/<_lang:\w+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>/<_lang:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<_lang:\w+>' => '<controller>/<action>',

                '/<_lang:\w+>'               => 'static/pages/index',
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
            'storageURL' => 'http://storage.skiliks.com/v1/'
        ],
        'simulation' => [
            'lite' => [
                'start' => '9:45',
                'end' => '12:45',
            ],
            'full' => [
                'start' => '9:45',
                'end' => '18:00',
            ]
        ],
        'zoho' => array(
            'apiKey'              => 'c076746cd578f7e9287ff1234d3faf2f',
            'saveUrl'             => 'http://live.skiliks.com/zoho/saveExcel',
            'xlsTemplatesDirPath' => 'documents/templates',
            'templatesDirPath'    => 'documents/zoho',
            //'sendFileUrl'         => 'https://presheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
            'sendFileUrl'         => 'http://zoho.skiliks.com/remotedoc.im?apikey=%s&output=editor',
            'extExcel'            => 'xls'

        ),
        'cron' => [
            'CleanUsers'=> 604800,
            'InviteExpired'=> 604800
        ],
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


