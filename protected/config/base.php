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
        'application.models.PaymentMethods.*',
        'application.models.Planner.*',
        'application.models.Documents.*',
        'application.models.GameEvents.*',
        'application.models.Interfaces.*',
        'application.models.Referrals.*',

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
        'application.components.Report2.*',
        'application.components.debug.*',

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

                'cheat/quick-start/full'                => 'static/cheats/startSimulationForFastSeleniumTest',

                'Admin/Log'              => 'static/Admin/Log',
                'logout'                  => 'static/userAuth/logout',

                'old-browser' => 'static/pages/oldBrowser',

                'profile/without-account' => 'static/site/runSimulationOrChooseAccount',

                'simulation/start'        => 'simulation/start',
                'simulation/stop'         => 'simulation/stop',
                'simulation/changeTime'   => 'simulation/changeTime',
                'simulation/startPause'   => 'simulation/startPause',
                'simulation/stopPause'    => 'simulation/stopPause',
                'simulation/exit'         => 'static/site/Exit',
                'simulation/demo'         => 'static/site/demo',
                'simulation/legacy/<mode:\w+>/<type:\w+>/<invite_id:\d+>' => 'static/pages/legacyAndTerms',

                'simulation/<mode:\w+>/<type:\w+>/<invite_id:\d+>' => 'static/site/simulation',
                'simulation/<mode:\w+>/<type:\w+>'                 => 'static/site/simulation',
                'promo/<mode:\w+>/<type:\w+>'                      => 'static/dashboard/index',

                'tariffs/<type:\w+>'                             => 'static/payment/changeTariff',
                'registration'                                   => 'static/userAuth/registration',
                'registration/by-link/<code:\w+>'                => 'static/userAuth/registerByLink',
                'register-referral/<refHash:\w+>'                => 'static/userAuth/registerReferral',
                'registration/account-type/added'                => 'static/userAuth/accountTypeSavesSuccessfully',
                'simulationIsStarted'                            => 'static/site/IsStarted',
                'userStartSecondSimulation'                      => 'static/site/UserStartSecondSimulation',
                'userRejectStartSecondSimulation'                => 'static/site/UserRejectStartSecondSimulation',
                'logout/registration'                            =>'static/userAuth/LogoutAndRegistration',


                'static/break-simulations-for-self-to-self-invites' =>'static/site/breakSimulationsForSelfToSelfInvites',

                'recovery'                  => 'static/userAuth/recovery',
                'results'                   =>'static/userAuth/results',
                'simulation/results'        =>'static/userAuth/results', // just to handle JS 'Sim stop'
                'site/results'              =>'static/userAuth/results',

                'userAuth/<action:\w+>'     => 'static/userAuth/<action>',
                'registration/<action:\w+>' => 'static/userAuth/<action>',

                'dashboard-new'                      => 'static/dashboard/corporateNew',
                'profile-corporate-tariff-new'       => 'static/profile/corporateTariffNew',
                'profile-corporate-company-info-new' => 'static/profile/corporateCompanyInfoNew',
                'profile-corporate-user-info-new'    => 'static/profile/corporatePersonalDataNew',
                'profile-corporate-password-new'     => 'static/profile/corporatePasswordNew',
                'profile-corporate-vacancies-new'    => 'static/profile/corporateVacanciesNew',
                'help/general'                       => 'static/help/general',
                'help/corporate'                     => 'static/help/corporate',
                'help/personal'                      => 'static/help/personal',
                'form-errors-standard'               => 'static/pages/formErrorsStandard',
                'product-new'                        => 'static/pages/productNew',
                'team-new'                           => 'static/pages/teamNew',
                'home-new'                           => 'static/pages/homeNew',
                'old-browser-new'                    => 'static/pages/oldBrowserNew',
                'static/tariffs-new'                 => 'static/pages/tariffsNew',
                'order-new/<tariffType:\w+>'         => 'static/payment/orderNew',
                'payment/doCashPayment'              => 'static/payment/doCashPayment',
                'payment/getRobokassaForm'           => 'static/payment/getRobokassaForm',
                'payment/success'                    => 'static/payment/success',
                'payment/fail'                       => 'static/payment/fail',
                'payment/result'                     => 'static/payment/result',
                'payment/invoiceSuccess'              => 'static/payment/InvoiceSuccess',

                'dashboard/'          => 'static/dashboard/index',
                'dashboard/corporate' => 'static/dashboard/corporate',
                'dashboard/inviteReferrals' => 'static/dashboard/inviteReferrals',
                'dashboard/sendReferralEmail' => 'static/dashboard/sendReferralEmail',
                'dashboard/personal'  => 'static/dashboard/personal',
                'dashboard/simulationdetails/<id:\w+>' => 'static/dashboard/simulationDetails',
                'dashboard/dontShowPopup' => 'static/dashboard/dontShowPopup',
                'dashboard/dontShowTariffEndPopup' => 'static/dashboard/dontShowTariffEndPopup',
                'dashboard/remakeRenderType' => 'static/dashboard/remakeRenderType',
                'invite/referrals' => 'static/dashboard/inviteReferrals',

                'profile/personal/personal-data/'  => 'static/profile/personalPersonalData',
                'profile/corporate/personal-data/' => 'static/profile/corporatePersonalData',
                'profile/corporate/referrals'                => 'static/profile/corporateReferrals',
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

                'dashboard/invite/remove/<id:\d+>/soft' => 'static/dashboard/softRemoveInvite',
                'dashboard/invite/remove/<inviteId:\w+>' => 'static/dashboard/removeInvite',
                'dashboard/invite/resend/<inviteId:\w+>' => 'static/dashboard/reSendInvite',

                'activation/resend/<profileId:\w+>' => 'static/userAuth/resendActivation',

                'dashboard/invite/remove/<inviteId:\w+>/<_lang:\w+>' => 'static/dashboard/removeInvite',
                'dashboard/invite/remove/<inviteId:\w+>/<_lang:\w+>' => 'static/dashboard/removeInvite',
                'dashboard/invite/resend/<inviteId:\w+>/<_lang:\w+>' => 'static/dashboard/reSendInvite',

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

                'logService/addInviteLog' => 'static/statistic/addInviteLog',

                'admin_area'                   => 'admin_area/AdminPages/Dashboard',
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
                'admin_area/import/'           => 'admin_area/AdminPages/ImportsList',
                'admin_area/send-notice/'      => 'admin_area/AdminPages/SendNotice',
                'admin_area/update-invite-email/'=>'admin_area/AdminPages/UpdateInviteEmail',
                'invite/add-10'                => 'admin_area/AdminPages/IncreaseInvites',
                'admin_area/live_simulations'  => 'admin_area/AdminPages/LiveSimulations',
                'admin_area/email_queue'       => 'admin_area/AdminPages/EmailQueue',
                'admin_area/completeInvoice'   => 'admin_area/AdminPages/CompleteInvoice',
                'admin_area/disableInvoice'    => 'admin_area/AdminPages/DisableInvoice',
                'admin_area/invoiceComment'    => 'admin_area/AdminPages/CommentInvoice',
                'admin_area/getInvoiceLog'     => 'admin_area/AdminPages/GetInvoiceLog',
                'admin_area/referrals'         => 'admin_area/AdminPages/ReferralsList',
                'admin_area/not-corporate-emails' => 'admin_area/AdminPages/NotCorporateEmails',
                'admin_area/RegistrationList'  => 'admin_area/AdminPages/RegistrationList',

                'admin_area/simulations/rating/csv'                       => 'admin_area/AdminPages/SimulationsRatingCsv',
                'admin_area/simulations/rating'                           => 'admin_area/AdminPages/SimulationsRating',
                'admin_area/email/<id:\w+>/text'                          => 'admin_area/AdminPages/EmailText',
                'admin_area/import-scenario/<slug:\w+>/<logImportId:\w+>' => 'admin_area/AdminPages/StartImport',
                'admin_area/import-log/<id:\w+>/get-text'                 => 'admin_area/AdminPages/GetImportLog',
                'admin_area/statistics/testAuth'                          => 'admin_area/AdminPages/TestAuth',
                'admin_area/statistics/statistic-order-count'             => 'admin_area/AdminPages/StatisticOrderCount',
                'admin_area/statistics/statistic-feedback-count'          => 'admin_area/AdminPages/StatisticFeedbackCount',
                'admin_area/statistics/statistic-crash-simulation'        => 'admin_area/AdminPages/StatisticCrashSimulation',
                'admin_area/statistics/statistic-mail'                    => 'admin_area/AdminPages/StatisticMail',
                'admin_area/statistics/free-disk-space'                   => 'admin_area/AdminPages/StatisticFreeDiskSpace',
                'admin_area/invite/<inviteId:\w+>/switch-can-be-reloaded' => 'admin_area/AdminPages/InviteSwitchCanBeReloaded',
                'admin_area/ban_user/<userId:\d+>'                        => 'admin_area/AdminPages/BanUser',

                'admin_area/corporate-accounts'                   => 'admin_area/AdminPages/CorporateAccountList',
                'admin_area/order/action/status'                  => 'admin_area/AdminPages/OrderActionStatus',
                'admin_area/invite/action/status'                 => 'admin_area/AdminPages/InviteActionStatus',
                'admin_area/invite/calculate/estimate'            => 'admin_area/AdminPages/InviteCalculateTheEstimate',
                'admin_area/invite/<invite_id:\w+>/site-logs'     => 'admin_area/AdminPages/SiteLogs',
                'admin_area/simulation/set-emergency/<simId:\d+>' => 'admin_area/AdminPages/SimulationSetEmergency',
                'admin_area/simulation/<sim_id:\w+>/site-logs'    => 'admin_area/AdminPages/SimSiteLogs',
                'admin_area/simulations'                          => 'admin_area/AdminPages/Simulations',
                'admin_area/simulations/<page:\d+>'               => 'admin_area/AdminPages/Simulations',
                'admin_area/simulation/<simId:\w+>/fixEndTime'    => 'admin_area/AdminPages/SimulationFixEndTime',
                'admin_area/simulation/<simId:\w+>/requests'      => 'admin_area/AdminPages/SimulationRequests',
                'admin_area/AdminPages/SubscribersList'           => 'admin_area/AdminPages/SubscribersList',
                'admin_area/user/<userId:\w+>/details'            => 'admin_area/AdminPages/UserDetails',
                'admin_area/user-referrals/<userId:\d+>'          => 'admin_area/AdminPages/UserReferrals',
                'admin_area/login/ghost/<userId:\d+>'             => 'admin_area/AdminPages/GhostLogin',

                'admin_area/user/<userId:\w+>/set-tariff/<label:\w+>'        => 'admin_area/AdminPages/UserSetTariff',
                'admin_area/user/<userId:\w+>/set-invites-limit/<value:[\w\-]+>' => 'admin_area/AdminPages/UserAddRemoveInvitations',

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
        'server_name'                   => 'http://skiliks.com/',
        'demoDuration'                  => 5, // min
        'disableOldLogging'             => false,
        'disableAssets'                 => false,
        'keep_last_category_time_214g'  => 90,
        'simulationStartUrl'            => '/index.php/simulation/start',
        'userNameInHeaderMaxLength'     => 30,

        // четвёртое приглашение значит что, 3 приглашения отправлены или пройдены в режиме сам-себе
        'countOfInvitesToShowReferralPopup'     => 4,

        'vacancyLinkInProfileMaxLength' => 50,
        'frontendUrl'                   => 'http://skiliks:8080/',
        'isUseResultPopUpCache'         => true,
        'isDisplaySimulationResults'    => false,
        'simulationIdStorage'           => 'request', // 'request', 'session'
        'initialSimulationsAmount'      => 3,

        // Блокирует/разрещает использование админами входа на сайт от именю любого пользователя
        'isBlockGhostLogin'             => false,

        // накладывает ограничение на круг лиц, которым разрешено использование GhostLogin
        'isUseStrictRulesForGhostLogin' => true,

        // ключь для передачи дополнительных данных в SiteHeart
        // ключь привязан к отделу
        // http://siteheart.com/ru/doc/sso
        'SiteHeartSecretKey'                 => 'qaDECE9Mk7',

        'emails' => [
            'isDisplayStandardInvitationMailTopText' => true, // 'Вопросы относительно вакансии вы можете задать по адресу %s, куратор вакансии - %s.'
            'inviteEmailTemplate'      => '//global_partials/mails/invite_default',
            'tariffExpiredTemplate'    => 'tariff_expired',
            'tariffExpiredTemplateIfInvitesZero' => 'tariff_expired_if_invites_zero',
            'newInvoiceToBooker'       => '//global_partials/mails/new_invoice',
            'completeInvoiceUserEmail' => '//global_partials/mails/completeInvoiceUserEmail',
            'referrerInviteEmail'      => '//global_partials/mails/referrerEmail',
            'noticeEmail'              => '//global_partials/mails/noticeEmail',
            'newFeedback'              => '//global_partials/mails/newFeedback',
            'ifSuspiciousActivity'     => '//global_partials/mails/ifSuspiciousActivity',

//            'bookerEmail' => 'accounter@skiliks.com',
            'bookerEmail' => 'invoice@skiliks.com',
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
            'runMigrationOn'                     => 'nobody', //production - skiliks.com, live - live.skiliks.com, loc - loc.skiliks.com
            'canIntroPassed'                     => true,
            'skiliksSpeedFactor'                 => 5,
            'skiliksDeveloperModeSpeedFactor'    => 8,
            'storageURL'                         => 'http://storage.skiliks.com/v1',
            'afterCallZoomerDuration'            => 2000, // milliseconds
            'isDisplayServer500errors'           => false,
            'isUseStrictAssertsWhenSimStop'      => false,
            'frontendAjaxTimeout'                => 10000, // 60 sec
            'simStartTimeout'                    => 180000,
            'simStopTimeout'                     => 10*60*1000,
            'useSentryForJsLog'                  => false,
            'isUseZohoProxy'                     => true,
            'isSkipBrowserCheck'                 => false,
            'isIncludeGoogleAnalyticsJavaScript' => false,
            'isDisplaySupportChat'               => true,
            'SiteHeartWidgetCode'                => '633075', // chat Skiliks(test mode): 626464, chat TechHelp (production mode): 633075
            'SiteHeartWidgetTitle'               => 'Онлайн помощь', // chat Skiliks(test mode): 626464, chat TechHelp (production mode): 633075
        ],
        'zoho' => array(
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
            'InviteExpired'=> 60*60*24*5,
        ],
        'initial_data' => [
            'users' => [
                /* is_admin = 1 -- user will be admin */
                ['username' => 'slavka'    , 'email' => 'slavka@skiliks.com'     ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'slavka1'   , 'email' => 'slavka1@skiliks.com'    ,'password' => '123123'         ,'is_admin' => 0],
                ['username' => 'asd'       , 'email' => 'asd@skiliks.com'        ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'selenium'  , 'email' => 'selenium@skiliks.com'   ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'listepo'   , 'email' => 'ivan@skiliks.com'       ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'tony'      , 'email' => 'tony@skiliks.com'       ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'leah'      , 'email' => 'leah.levin@skiliks.com' ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'masha'     , 'email' => 'masha@skiliks.com'      ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'tatiana'   , 'email' => 'tatiana@skiliks.com'    ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'svetlana'  , 'email' => 'svetlana@skiliks.com'   ,'password' => '123123'         ,'is_admin' => 0],
                ['username' => 'vladimir'  , 'email' => 'vladimir@skiliks.com'   ,'password' => '123123'         ,'is_admin' => 1],
                ['username' => 'vladimir1' , 'email' => 'vladimir1@skiliks.com'  ,'password' => '123123'         ,'is_admin' => 1],
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
        'robokassa' => [
                'url'            => 'http://test.robokassa.ru/Index.aspx',
                'MrchLogin'      => 'skiliks_dev',
                'Desc'           => 'Оплата согласно...',
                'sMerchantPass1' => 'dcZz6P318a',
                'sMerchantPass2' => 'S358oP0ikj'
              ]
//            Test
//            [
//                'MrchLogin'      => 'skiliks_dev',
//                'Desc'           => 'Оплата согласно...',
//                'sMerchantPass1' => 'dcZz6P318a',
//                'sMerchantPass2' => 'S358oP0ikj'
//            ]
    ),
);


