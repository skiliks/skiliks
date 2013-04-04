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
        'application.models.Exceptions.*',
        'application.models.Logs.*',
        'application.models.Performance.*',
        'application.models.Stress.*',
        'application.models.Simulation.*',
        'application.models.Mail.*',
        'application.models.Activities.*',
        'application.models.UserAccounts.*',
        'application.models.Planner.*',
        'application.models.Documents.*',
        'application.models.GameEvents.*',
        'application.models.Interfaces.*',

        'application.components.*',
        'application.components.Zoho.*',
        'application.components.Tools.*',
        'application.components.Excel.*',
        'application.components.Email.*',
        'application.components.Plan.*',
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
            'password' =>'skiliks531',
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
    'sourceLanguage'    =>'en',
    'language'          =>'ru',
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
                'static/tariffs/<_lang:\w+>' => 'static/pages/tariffs',

                'static/team/'    => 'static/pages/team',
                'static/product/' => 'static/pages/product',
                'static/tariffs/' => 'static/pages/tariffs',

                'static/charts' => 'static/pages/charts',

                'subscription/add'       => 'static/pages/addUserSubscription',
                'static/pages/addUserSubscription' => 'static/pages/addUserSubscription', // strange, but works in this way only

                'static/comingSoonSuccess' => 'static/pages/comingSoonSuccess',
                'static/comingSoonSuccess/<_lang:\w+>' => 'static/pages/comingSoonSuccess',

                'static/<_lang:\w+>'         => 'static/pages/index',

                'admin/'                  => 'static/admin/',
                'admin/displayLog/<simulation\w+>' => 'static/admin/displayLog',
                'static/admin/saveLog/<simulation\w+>' => 'static/admin/saveLog',
                'cheat/dialogsAnalyzer'   => 'static/admin/dialogsAnalyzer',
                'cheat/uploadDialogsToAnalyzer'   => 'static/admin/uploadDialogsToAnalyzer',
                'Admin/Log'              => 'static/Admin/Log',

                'logout'                  => 'static/userAuth/logout',

                'bad-browser' => 'static/pages/badBrowser',
                'old-browser' => 'static/pages/oldBrowser',

                'profile/without-account' => 'static/site/runSimulationOrChooseAccount',

                'simulation/start'        => 'simulation/start',
                'simulation/stop'         => 'simulation/stop',
                'simulation/changeTime'   => 'simulation/changeTime',
                'simulation/startPause'   => 'simulation/startPause',
                'simulation/stopPause'    => 'simulation/stopPause',
                'simulation/legacy/<mode:\w+>/<type:\w+>/<invite_id:\d+>' => 'static/pages/legacyAndTerms',

                'simulation/<mode:\w+>/<type:\w+>/<invite_id:\d+>'=> 'static/site/simulation',
                'simulation/<mode:\w+>/<type:\w+>'=> 'static/site/simulation',

                'registration'                         => 'static/userAuth/registration',
                'registration/by-link/<code:\w+>'      => 'static/userAuth/registerByLink',
                'registration/choose-account-type'     => 'static/userAuth/chooseAccountType',
                'registration/account-type/added'      => 'static/userAuth/accountTypeSavesSuccessfully',
                'registration/confirm-corporate-email' => 'static/userAuth/ConfirmCorporateEmail',
                'registration/confirm-corporate-email-success'=>'static/userAuth/ConfirmCorporateEmailSuccess',
                '/recovery' => 'static/userAuth/recovery',

                'results'                   =>'static/userAuth/results',
                'simulation/results'        =>'static/userAuth/results', // just to handle JS 'Sim stop'
                'site/results'              =>'static/userAuth/results',

                'userAuth/<action:\w+>'    => 'static/userAuth/<action>',
                'registration/<action:\w+>'   => 'static/userAuth/<action>',

                'cheats'          => 'static/cheats/mainPage',
                'cheats/cleanUpAccount'          => 'static/cheats/cleanUpAccount',
                'cheats/setinvites/<status:\w+>' => 'static/cheats/setStatusForAllInvites',
                'static/cheats/set-tariff/' => 'static/cheats/chooseTariff',
                'static/cheats/set-tariff/<label:\w+>' => 'static/cheats/chooseTariff',

                'dashboard/'          => 'static/dashboard/index',
                'dashboard/corporate' => 'static/dashboard/corporate',
                'dashboard/personal'  => 'static/dashboard/personal',

                'profile/personal/personal-data/'  => 'static/profile/personalPersonalData',
                'profile/corporate/personal-data/' => 'static/profile/corporatePersonalData',
                'profile/corporate/password/' => 'static/profile/corporatePassword',
                'profile/personal/password/'  => 'static/profile/personalPassword',
                'profile/corporate/company-info/' => 'static/profile/corporateCompanyInfo',
                'profile/corporate/vacancies/'  => 'static/profile/corporateVacancies',
                'profile/corporate/tariff/'  => 'static/profile/corporateTariff',
                'profile/corporate/payment-method/'  => 'static/profile/corporatePaymentMethod',

                'profile/getSpecialization'       => 'static/profile/getSpecialization',
                'profile/corporate/vacancy/<id:\w+>/remove' => 'static/profile/removeVacancy',
                'profile/corporate/vacancy/<id:\w+>/edit' => 'static/profile/corporateVacancies',

                'profile/'          => 'static/profile/index',
                'profile/corporate' => 'static/profile/index',
                'profile/personal'  => 'static/profile/index',

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

                'dashboard/invite/remove/<id:\d+>/soft' => 'static/dashboard/softRemoveInvite',
                'dashboard/invite/remove/<inviteId:\w+>' => 'static/dashboard/removeInvite',
                'dashboard/invite/resend/<inviteId:\w+>' => 'static/dashboard/reSendInvite',

                'dashboard/invite/remove/<inviteId:\w+>/<_lang:\w+>' => 'static/dashboard/removeInvite',
                'dashboard/invite/resend/<inviteId:\w+>/<_lang:\w+>' => 'static/dashboard/reSendInvite',

                'invite/add-10'                => 'static/cheats/increaseInvites',

                'dashboard/decline-invite/validation' => 'static/dashboard/validateDeclineExplanation',
                'dashboard/send-invite'               => 'static/dashboard/sendInviteEmail',
                'dashboard/accept-invite/<id:\w+>'  => 'static/dashboard/acceptInvite',
                'dashboard/decline-invite/<id:\w+>' => 'static/dashboard/declineInvite',

                'gii'=>'gii',
                'gii/<controller:\w+>'=>'gii/<controller>',
                'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',

                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',

                '<controller:\w+>/<id:\d+>/<_lang:\w+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>/<_lang:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<_lang:\w+>' => '<controller>/<action>',

                '/<_lang:(ru|en)>'               => 'static/pages/index',
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
        'allowedLanguages' => [
            'en' => [
                'static/pages/index',
                'static/pages/team',
                'static/pages/product'
            ]
        ],
        // This part will be sent to JS
        'public' => [
            'skiliksSpeedFactor' => 8,
            'storageURL' => 'http://storage.skiliks.com/v1/'
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
                ['username' => 'gugu'      , 'email' => 'gugu@skiliks.com'     ,'password' => 'gfhjkm'         ,'is_admin' => 1],
                ['username' => 'slavka'    , 'email' => 'slavka@skiliks.com'   ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'slavka1'   , 'email' => 'slavka1@skiliks.com'   ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'asd'       , 'email' => 'asd@skiliks.com'      ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'vad'       , 'email' => 'vad@skiliks.com'      ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'listepo'   , 'email' => 'ivan@skiliks.com'     ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'tony'      , 'email' => 'tony@skiliks.com'     ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'leah'      , 'email' => 'leah@skiliks.com'     ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'masha'     , 'email' => 'masha@skiliks.com'    ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'pernifin'  , 'email' => 'pernifin@skiliks.com' ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'kirill'    , 'email' => 'kirill@skiliks.com'   ,'password' => 'wu-wod-bo-slyub','is_admin' => 1],
                ['username' => 'tatiana'   , 'email' => 'tatiana@skiliks.com'  ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'ahmed'     , 'email' => 'ahmed@zoho.com'       ,'password' => 'zohozoho'       ,'is_admin' => 1],
                ['username' => 'rkilimov'  , 'email' => 'r.kilimov@gmail.com'  ,'password' => 'r.kilimov'      ,'is_admin' => 1],
                ['username' => 'svetlana'  , 'email' => 'svetlana@skiliks.com' ,'password' => '123123'         ,'is_admin' => 1],
            ]
        ],
        'test_mappings' => require(dirname(__FILE__) . '/test_mappings.php'),
        'analizer' => array(
             'excel' => array(
                 'consolidatedBudget' => array(
                     'worksheetNames' => array(
                        'consolidated' => 'сводный',
                        'sales'        => 'продажи',
                        'production'   => 'производство',
                        'logistic'     => 'логистика',
                        'other'        => 'прочее',
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


