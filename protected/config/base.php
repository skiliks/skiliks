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
        'application.components.Exception.*',
        'application.components.Controllers.*',
        'application.components.SocialCalc.*',
        'application.extensions.*',
        'application.extensions.PHPExcel.*',
        'application.extensions.phpmailer.*',
        'application.vendors.*',

        'application.modules.user.models.*',
        'application.modules.user.components.*',
        'application.modules.avatar.models.*',
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
            //'ipFilters'=>array('62.205.135.161'),
            // 'newFileMode'=>0666,
            // 'newDirMode'=>0777,
        ),
        'user' => array(
            'debug'                 => false,
            'activationPasswordSet' => false,
            'mailer'                => 'PHPMailer',
            'loginType'             => 2,
            'returnLogoutUrl'       => '/',
            'phpmailer'             => array(
                'transport'  => 'smtp',
                'html'       => true,
                'properties' => array(
                    'CharSet'    => 'UTF-8', // SMTP server
                    'SMTPDebug'  => false,          // enables SMTP debug information (for testing)
                    'SMTPAuth'   => true,            // enable SMTP authentication
                    'SMTPSecure' => 'tls',         // sets the prefix to the servier
                    'Host'       => 'smtp.yandex.ru',                   // set the SMTP port for the GMAIL server
                    'Username'   => 'support@skiliks.com',  // GMAIL username
                    'Password'   => 'skiliks531',        // GMAIL password
                ),
                'msgOptions'=>array(
                    'fromName' =>'Skiliks',
                    'toName'   =>'',
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
            'class'                  => 'CDbHttpSession',
            'autoCreateSessionTable' => false,
            'connectionID'           => 'db',
            'sessionName'            => 'sid',
            'cookieMode'             => 'allow',
            'timeout'                => 60*60*24*31, // 1 mouth
        ),
        'user' => array(
            'class'          => 'application.modules.user.components.YumWebUser',
            'allowAutoLogin' => true,
            // 'loginUrl'    => array('//user/user/login'),
        ),
        'urlManager' => array(
            'urlFormat'      => 'path',
            'showScriptName' => false,

            'rules' => array(
                '/'               => 'static/pages/index',
                'static/'         => 'static/pages/index',

                'static/team/<_lang:\w+>'    => 'static/pages/team',
                'static/product/<_lang:\w+>' => 'static/pages/product',
                'static/tariffs/<_lang:\w+>' => 'static/pages/tariffs',
                'static/terms'               => 'static/pages/terms',
                'static/feedback'            => 'static/pages/feedback',

                'static/drag-and-drop'            => 'static/pages/dragAndDropPrototype',

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
                'admin/displayLog/<simulation:\w+>' => 'static/admin/displayLog',
                'static/admin/saveLog/<simulation:\w+>' => 'static/admin/saveLog',

                'cheat/dialogsAnalyzer'                 => 'static/admin/dialogsAnalyzer',
                'cheat/uploadDialogsToAnalyzer'         => 'static/admin/uploadDialogsToAnalyzer',
                'cheat/assessments/grid'                => 'static/cheats/assessmentsGrid',
                'cheat/quick-start/full'                => 'static/cheats/startSimulationForFastSeleniumTest',
                'cheat/zoho/getUsageValue'              => 'static/cheats/getZohoUsageStatus',

                'cheat/zoho/saveUsageValue/<value:\w+>/<expireDate:\w+>' => 'static/cheats/saveZohoUsageStatus',

                'Admin/Log'              => 'static/Admin/Log',
                'logout'                  => 'static/userAuth/logout',

                'old-browser' => 'static/pages/oldBrowser',

                'profile/without-account' => 'static/site/runSimulationOrChooseAccount',

                'simulation/start'        => 'simulation/start',
                'simulation/stop'         => 'simulation/stop',
                'simulation/changeTime'   => 'simulation/changeTime',
                'simulation/startPause'   => 'simulation/startPause',
                'simulation/stopPause'    => 'simulation/stopPause',
                'simulation/legacy/<mode:\w+>/<type:\w+>/<invite_id:\d+>' => 'static/pages/legacyAndTerms',

                'simulation/<mode:\w+>/<type:\w+>/<invite_id:\d+>' => 'static/site/simulation',
                'simulation/<mode:\w+>/<type:\w+>'                 => 'static/site/simulation',
                'promo/<mode:\w+>/<type:\w+>'                      => 'static/dashboard/index',

                'tariffs/<type:\w+>'                   => 'static/payment/changeTariff',
                'registration'                         => 'static/userAuth/registration',
                'registration/by-link/<code:\w+>'      => 'static/userAuth/registerByLink',
                'registration/choose-account-type'     => 'static/userAuth/chooseAccountType',
                'registration/account-type/added'      => 'static/userAuth/accountTypeSavesSuccessfully',
                'registration/confirm-corporate-email' => 'static/userAuth/ConfirmCorporateEmail',
                'simulationIsStarted' => 'static/site/IsStarted',
                'userStartSecondSimulation' => 'static/site/UserStartSecondSimulation',
                'userRejectStartSecondSimulation' => 'static/site/UserRejectStartSecondSimulation',
                'logout/registration'                          =>'static/userAuth/LogoutAndRegistration',

                'recovery'                  => 'static/userAuth/recovery',
                'results'                   =>'static/userAuth/results',
                'simulation/results'        =>'static/userAuth/results', // just to handle JS 'Sim stop'
                'site/results'              =>'static/userAuth/results',

                'userAuth/<action:\w+>'     => 'static/userAuth/<action>',
                'registration/<action:\w+>' => 'static/userAuth/<action>',

                'cheats'                               => 'static/cheats/mainPage',
                'cheats/cleanUpAccount'                => 'static/cheats/cleanUpAccount',
                'cheats/setinvites/<status:\w+>'       => 'static/cheats/setStatusForAllInvites',
                'static/cheats/set-tariff/<label:\w+>' => 'static/cheats/chooseTariff',
                'static/cheats/set-tariff/'            => 'static/cheats/chooseTariff',
                'static/cheats/listOfsubscriptions'    => 'static/cheats/listOfsubscriptions',

                'dashboard-new'                      => 'static/dashboard/corporateNew',
                'simulations-new'                    => 'static/simulations/indexNew',
                'profile-corporate-tariff-new'       => 'static/profile/corporateTariffNew',
                'profile-corporate-company-info-new' => 'static/profile/corporateCompanyInfoNew',
                'profile-corporate-user-info-new'    => 'static/profile/corporatePersonalDataNew',
                'profile-corporate-password-new'     => 'static/profile/corporatePasswordNew',
                'profile-corporate-vacancies-new'    => 'static/profile/corporateVacanciesNew',
                'form-errors-standard'               => 'static/pages/formErrorsStandard',
                'product-new'                        => 'static/pages/productNew',
                'team-new'                           => 'static/pages/teamNew',
                'old-browser-new'                    => 'static/pages/teamNew',
                'home-new'                           => 'static/pages/homeNew',
                'old-browser-new'                    => 'static/pages/oldBrowserNew',
                'static/tariffs-new'                 => 'static/pages/tariffsNew',
                'order-new/<tariffType:\w+>'         => 'static/payment/orderNew',

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

                'vacancy/add'                     => 'static/profile/vacancyAdd',

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
                'fail-recovery/'  => 'static/userAuth/FailRecovery',

                'simulations/'                 => 'static/simulations/index',
                'simulations/details/<id:\w+>' => 'static/simulations/details',
                'simulations/corporate'        => 'static/simulations/corporate',
                'simulations/personal'         => 'static/simulations/personal',

                'dashboard/invite/remove/<id:\d+>/soft' => 'static/dashboard/softRemoveInvite',
                'dashboard/invite/remove/<inviteId:\w+>' => 'static/dashboard/removeInvite',
                'dashboard/invite/resend/<inviteId:\w+>' => 'static/dashboard/reSendInvite',

                'activation/resend/<profileId:\w+>' => 'static/userAuth/resendActivation',

                'dashboard/invite/remove/<inviteId:\w+>/<_lang:\w+>' => 'static/dashboard/removeInvite',
                'dashboard/invite/resend/<inviteId:\w+>/<_lang:\w+>' => 'static/dashboard/reSendInvite',

                'invite/add-10'                => 'static/cheats/increaseInvites',

                'dashboard/decline-invite/validation' => 'static/dashboard/validateDeclineExplanation',
                'dashboard/send-invite'               => 'static/dashboard/sendInviteEmail',
                'dashboard/accept-invite/<id:\w+>'  => 'static/dashboard/acceptInvite',
                'dashboard/decline-invite/<id:\w+>' => 'static/dashboard/declineInvite',

                'payment/order/<tariffType:\w+>'       => 'static/payment/order',
                'payment/do'                           => 'static/payment/do',

                'statistics/phpUnitTests' => 'statistics/statistics/phpUnitTests',
                'statistics/SeleniumTests' => 'statistics/statistics/SeleniumTests',
                'statistics/SeleniumTestsAuth' => 'statistics/statistics/SeleniumTestsAuth',
                'statistics/Zoho500' => 'statistics/statistics/Zoho500',
                'statistics/CiTests' => 'statistics/statistics/CiTests',
                'statistics/OrderCount' => 'statistics/statistics/OrderCount',
                'statistics/FeedbackCount' => 'statistics/statistics/FeedbackCount',

                'admin_area/invites'           => 'admin_area/AdminPages/Invites',
                'admin_area/dashboard'         => 'admin_area/AdminPages/Dashboard',
                'admin_area/login'             => 'admin_area/AdminPages/Login',
                'admin_area/logout'            => 'admin_area/AdminPages/Logout',
                'admin_area/simulation_detail' => 'admin_area/AdminPages/SimulationDetail',
                'admin_area/invites/save'      => 'admin_area/AdminPages/InvitesSave',
                'admin_area/budget'            => 'admin_area/AdminPages/GetBudget',
                'admin_area/invite/reset'      => 'admin_area/AdminPages/ResetInvite',
                'admin_area/orders'            => 'admin_area/AdminPages/Orders',
                'admin_area/order/checked'     => 'admin_area/AdminPages/OrderChecked',
                'admin_area/order/unchecked'   => 'admin_area/AdminPages/OrderUnchecked',
                'admin_area/users'             => 'admin_area/AdminPages/UsersList',
                'admin_area/feedbacks'         => 'admin_area/AdminPages/FeedBacksList',
                'admin_area/statistics'        => 'admin_area/AdminPages/Statistics',
                'admin_area/statistics/testAuth'        => 'admin_area/AdminPages/TestAuth',
                'admin_area/statistics/statistic-order-count'        => 'admin_area/AdminPages/StatisticOrderCount',
                'admin_area/statistics/statistic-feedback-count'        => 'admin_area/AdminPages/StatisticFeedbackCount',
                'admin_area/statistics/statistic-crash-simulation'        => 'admin_area/AdminPages/StatisticCrashSimulation',
                'admin_area/statistics/free-disk-space'        => 'admin_area/AdminPages/StatisticFreeDiskSpace',

                'admin_area/corporate-accounts'                   => 'admin_area/AdminPages/CorporateAccountList',
                'admin_area/order/action/status'                  => 'admin_area/AdminPages/OrderActionStatus',
                'admin_area/invite/action/status'                 => 'admin_area/AdminPages/InviteActionStatus',
                'admin_area/invite/calculate/estimate'            => 'admin_area/AdminPages/InviteCalculateTheEstimate',
                'admin_area/invite/<invite_id:\w+>/site-logs'     => 'admin_area/AdminPages/SiteLogs',
                'admin/invite/<inviteId:\w+>/switch-can-be-reloaded' => 'admin_area/AdminPages/InviteSwitchCanBeReloaded',
                'admin_area/simulation/set-emergency/<simId:\d+>' => 'admin_area/AdminPages/SimulationSetEmergency',
                'admin_area/simulation/<sim_id:\w+>/site-logs'    => 'admin_area/AdminPages/SimSiteLogs',
                'admin_area/simulations'                          => 'admin_area/AdminPages/Simulations',
                'admin_area/simulations/<page:\d+>'               => 'admin_area/AdminPages/Simulations',
                'admin_area/simulation/<simId:\w+>/fixEndTime'    => 'admin_area/AdminPages/SimulationFixEndTime',
                'admin_area/simulation/<simId:\w+>/requests'      => 'admin_area/AdminPages/SimulationRequests',
                'cache.manifest'                                  => 'static/ApplicationCache/Manifest',
                'page_for_cache'                                  => 'static/ApplicationCache/PageForCache',

                'admin_area/corporate-account/<id:\w+>/invite-limit-logs' => 'admin_area/AdminPages/CorporateAccountInviteLimitLogs',
                'admin_area/site-user/<userId:\w+>/update-password'           => 'admin_area/AdminPages/UpdatePassword',

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
        'clientScript' => array(
            'class' => 'ext.yii-less-extension.components.YiiLessCClientScript',
            'cache' => true,
            'basePath' => realpath(dirname(__FILE__) . '/../..')
        ),
        'errorHandler'=>array(
            'errorAction' => 'static/site/error404'
        ),
    ),
    'basePath' => dirname(__FILE__) . '/..',

    'preload' => array('log'),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        'disableAssets'                 => false,
        'keep_last_category_time_214g'  => 60,
        'simulationStartUrl'            => '/index.php/simulation/start',
        'userNameInHeaderMaxLength'     => 30,
        'vacancyLinkInProfileMaxLength' => 50,
        'frontendUrl'                   => 'http://skiliks:8080/',
        'isUseResultPopUpCache'         => true,
        'emails' => [
            'isDisplayStandardInvitationMailTopText' => false, // 'Вопросы относительно вакансии вы можете задать по адресу %s, куратор вакансии - %s.'
            'defaultMessageText' => 'Продолжая начатую в 2012 году программу корпоративного развития, предлагаем Вам поучаствовать в прохождении тестовой версии симуляции компании "Скиликс".<br/>'.
            'Это компьютерная игра, по результатам которой планируется уточнить, что для Вас является зоной ближайшего развития и на чем нужно сосредоточиться для достижения лучших результатов.<br/>'.
            'Также это поможет скорректировать и уточнить цели и задачи внутрифирменного и внешнего обучения.',
            'inviteEmailTemplate' => '//global_partials/mails/invite_eksmo', // '//global_partials/mails/invite_default'
        ],
        'allowedLanguages' => [
            'en' => [
                'static/pages/comingSoonSuccess',
                'static/pages/index',
                'static/pages/team',
                'static/pages/product',
                'static/pages/tariffs',
                'static/pages/feedback',
            ]
        ],
        // This part will be sent to JS
        'public' => [
            'skiliksSpeedFactor'                 => 5,
            'skiliksDeveloperModeSpeedFactor'    => 8,
            'storageURL'                         => 'http://storage.skiliks.com/v1',
            'afterCallZoomerDuration'            => 2000, // milliseconds
            'isDisplayServer500errors'           => false,
            'isUseStrictAssertsWhenSimStop'      => false,
            'frontendAjaxTimeout'                => 60000, // 60 sec
            'useSentryForJsLog'                  => false,
            'isUseZohoProxy'                     => true,
            'isSkipBrowserCheck'                 => false,
            'isIncludeGoogleAnalyticsJavaScript' => false,
        ],
        'zoho' => array(
            //'apiKey'              => 'c998c211c5404969606b6738c106c183',
            'apiKey'              => 'e52059ce3aeff6dd2c71afb9499bdcf7', //old
            'saveUrl'             => 'http://stage.skiliks.com/zoho/saveExcel',
            'xlsTemplatesDirPath' => 'documents/templates',
            'templatesDirPath'    => 'documents/zoho',
            'sendFileUrl'         => 'https://sheet.zoho.com/remotedoc.im?apikey=%s&output=editor',
            //'sendFileUrl'         => 'http://zoho.skiliks.com/remotedoc.im?apikey=%s&output=editor',
            'extExcel'            => 'xls'

        ),
        'cron' => [
//            'CleanUsers'=> 604800,8
            'InviteExpired'=> 604800
        ],
        'initial_data' => [
            'users' => [
                /* is_admin = 1 -- user will be admin */
                ['username' => 'slavka'    , 'email' => 'slavka@skiliks.com'   ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'slavka1'   , 'email' => 'slavka1@skiliks.com'  ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'asd'       , 'email' => 'asd@skiliks.com'      ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'selenium'  , 'email' => 'selenium@skiliks.com' ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'listepo'   , 'email' => 'ivan@skiliks.com'     ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'tony'      , 'email' => 'tony@skiliks.com'     ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'leah'      , 'email' => 'leah@skiliks.com'     ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'masha'     , 'email' => 'masha@skiliks.com'    ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'pernifin'  , 'email' => 'pernifin@skiliks.com' ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'tatiana'   , 'email' => 'tatiana@skiliks.com'  ,'password' => '123123'         ,'is_admin' => 1],
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


