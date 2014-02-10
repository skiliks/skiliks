<?php
/**
 * Base app config
 */
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', true);
defined('YII_ENABLE_EXCEPTION_HANDLER') or define('YII_ENABLE_EXCEPTION_HANDLER', true);
ini_set('date.timezone', 'Europe/Moscow');

return CMap::mergeArray(
    require(dirname(__FILE__) . '/_routs.php'),
    require(dirname(__FILE__) . '/_autoloader.php'),
    array(
        'modules'=>array(
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
                        'CharSet'    => 'UTF-8',               // SMTP server
                        'SMTPDebug'  => false,                 // enables SMTP debug information (for testing)
                        'SMTPAuth'   => true,                  // enable SMTP authentication
                        'SMTPSecure' => 'tls',                 // sets the prefix to the servier
                        'Host'       => 'smtp.yandex.ru',      // set the SMTP port for the yandex server
                        'Username'   => 'support@skiliks.com', // yandex username
                        'Password'   => 'deNejna1a',          // yandex password
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
            /** @link: http://www.yiiframework.com/doc/api/1.1/CWebUser */
            'user' => array(
                'class'           => 'application.modules.user.components.YumWebUser',

                // whether to enable cookie-based login.
                'allowAutoLogin'  => true,

                // whether to automatically renew the identity cookie each time a page is requested.
                'autoRenewCookie' => true,
            ),
            'session' => array(
                'class'                  => 'CDbHttpSession',
                'autoCreateSessionTable' => false,
                'connectionID'           => 'db',
                'sessionName'            => 'sid',
                'cookieMode'             => 'allow',
                'timeout'                => 60*60*24*7, // 7 days
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
            // имя сервера, стоб понимать с какого сервера пришли письма про подозрительную активность
            'server_name'        => 'не задан', // формат 'http://domain.com/'
            'server_domain_name' => 'не задан', // формат 'domain.com'

            // просто подпись на сайте, вынесена в конфиг - чтоб было проще править
            'demoDuration'                  => 5, // min

            // отключает проверку
            'disableAssets'                 => false,

            // время keep_last_category в минутах
            'keep_last_category_time_214g'  => 90,

            // ???
            'simulationStartUrl'            => '/index.php/simulation/start',

            // Максимальная длинна имени
            'userNameInHeaderMaxLength'     => 30,

            // четвёртое приглашение значит что, 3 приглашения отправлены или пройдены в режиме сам-себе
            'countOfInvitesToShowReferralPopup'     => 4,

            // точно используется?
            'vacancyLinkInProfileMaxLength' => 50,

            // используется для селениум-теcтов
            // формат 'http://domain.com:port/'
            'frontendUrl'                   => 'http://skiliks:8080/',

            // удалять результаты симуляции если селениум-тест успешно прошел false = не удалять
            'deleteSeleniumResults' => false,

            // Нужно для unit тестов. Вообще simulationId всегда передаётся с запросом.
            'simulationIdStorage'           => 'request', // 'request', 'session'

            // количество симуляций, которое даётся корпоративному пользователю после регистрации
            'initialSimulationsAmount'      => 3,


            'inviteExpired'                 => 5,

            // Блокирует/разрещает использование админами входа на сайт от именю любого пользователя
            'isBlockGhostLogin'             => false,

            // накладывает ограничение на круг лиц, которым разрешено использование GhostLogin
            'isUseStrictRulesForGhostLogin' => true,

            // ключь для передачи дополнительных данных в SiteHeart
            // ключь привязан к отделу
            // http://siteheart.com/ru/doc/sso
            'SiteHeartSecretKey'                 => 'qaDECE9Mk7',

            // CSS класс который доавляется во все BODY на сайте
            'css-theme' => '',

            // ???
            'emails' => [
                'isDisplayStandardInvitationMailTopText' => true, // 'Вопросы относительно вакансии вы можете задать по адресу %s, куратор вакансии - %s.'

                'newInvoiceToBooker'         => '//global_partials/mails/new_invoice',

                // Емейл бухгалтера
                'bookerEmail' => 'invoice@skiliks.com',
            ],

            // страницы для которых надо показывать переключатель языка
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

            'imagesForPreLoad' => [
                'img/papka-small.png',
                'img/pause.png',
                'img/phone-small.png',
                'img/plan-small.png',
                'img/pochta-small.png',
                'img/workplace-small.png',
                'img/doc-icons.png',
                'img/doc-icons-mini.png',
                'img/window-title-dots.png',
                'img/main-screen/bg-main-light.jpg',

                // SocialCacl:
                'img/excel',

                'img/manual',

                'img/documents',
                'img/interface',
                'img/mail',
                'img/main-screen',
                'img/phone',
                'img/planner',
                'img/tag-handler',

                'img/visitor',
            ],

            // This part will be sent to JS
            'public' => [
                'runMigrationOn'                     => 'nobody', //production - skiliks.com, live - live.skiliks.com, loc - loc.skiliks.com

                // Позволено ли игроку пропустить интро видео
                'canIntroPassed'                     => true,

                // Множитель скорости времени в PROMO симуляции
                'skiliksSpeedFactor'                 => 5,

                // Множитель скорости времени в DEVELOP симуляции
                'skiliksDeveloperModeSpeedFactor'    => 8,

                // Адрес хранилища видео и звуков
                'storageURL'                         => 'http://storage.skiliks.com/',

                // Показывать ли 500 ошибки с сервера в виде сообщений в игре
                'isDisplayServer500errors'           => false,

                'isUseStrictAssertsWhenSimStop'      => false,

                'frontendAjaxTimeout'                => 10000, // 60 sec

                'simStartTimeout'                    => 180000,

                'simStopTimeout'                     => 10*60*1000,

                'useSentryForJsLog'                  => false,

                'isSkipBrowserCheck'                 => false,

                'isSkipSpeedTest'                    => false,

                'isSkipOsCheck'                      => false, //Проверка ОС перед стартом игры

                // позволяет отобразить это JS только на продакшене
                'isIncludeGoogleAnalyticsJavaScript' => false,

                'isDisplaySupportChat'               => true,

                'SiteHeartWidgetCode'                => '633075', // chat Skiliks(test mode): 626464, chat TechHelp (production mode): 633075

                'SiteHeartWidgetTitle'               => 'Онлайн помощь', // chat Skiliks(test mode): 626464, chat TechHelp (production mode): 633075
            ],
            'cron' => [
                // через сколько секунд устаревает приглашение
                'InviteExpired'=> 60*60*24*5,
            ],

            'initial_data' => [
                'users' => [
                    /* is_admin = 1 -- user will be admin */
                    ['username' => 'slavka'    , 'email' => 'slavka@skiliks.com'            ,'password' => 'skiliks123123'         ,'is_admin' => 1],
                    ['username' => 'slavka1'   , 'email' => 'slavka1@skiliks.com'           ,'password' => 'skiliks123123'         ,'is_admin' => 0],
                    ['username' => 'asd'       , 'email' => 'asd@skiliks.com'               ,'password' => 'skiliks123123'         ,'is_admin' => 1],
                    ['username' => 'seleniumEngine'  , 'email' => 'selenium.engine@skiliks.com'          ,'password' => 'skiliks123123'         ,'is_admin' => 1],
                    ['username' => 'listepo'   , 'email' => 'ivan@skiliks.com'              ,'password' => 'skiliks123123'         ,'is_admin' => 1],
                    ['username' => 'tony'      , 'email' => 'tony@skiliks.com'              ,'password' => 'skiliks123123'         ,'is_admin' => 1],
                    ['username' => 'leah'      , 'email' => 'leah.levin@skiliks.com'        ,'password' => 'skiliks123123'         ,'is_admin' => 1],
                    ['username' => 'masha'     , 'email' => 'masha@skiliks.com'             ,'password' => 'skiliks123123'         ,'is_admin' => 1],
                    ['username' => 'tatiana'   , 'email' => 'tetyana.grybok@skiliks.com'           ,'password' => 'skiliks123123'         ,'is_admin' => 1],
                    ['username' => 'andrew'    , 'email' => 'andrey.sarnavskiy@skiliks.com' ,'password' => 'skiliks123123'         ,'is_admin' => 1],
                    ['username' => 'seleniumAssessment'  , 'email' => 'selenium.assessment@skiliks.com'          ,'password' => 'skiliks123123'         ,'is_admin' => 1]
                ]
            ],

            'test_mappings' => require(dirname(__FILE__) . '/test_mappings.php'),

            'analizer' => array(
                // данные для анализа екселя
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
                        'limitToGetPoints'  => 0.2
                    ]
                ]
            ),
            'robokassa' => [
                'url'            => 'http://test.robokassa.ru/Index.aspx',
                'MerchantLogin'  => 'skiliks_dev',
                'Description'    => 'Оплата согласно...',
                'sMerchantPass1' => 'dcZz6P318a',
                'sMerchantPass2' => 'S358oP0ikj'
            ],
            'assessment_engine_version' => 'v2', //версия оценки(используеться в выводе попапа с оценкой)
            'max_auth_failed_attempt' => 5, //Максимальное число попыток авторизации
    //            Test robokassa
    //            [
    //                'url'            => 'http://test.robokassa.ru/Index.aspx',
    //                'MerchantLogin'  => 'skiliks_dev',
    //                'Description'    => 'Оплата согласно...',
    //                'sMerchantPass1' => 'dcZz6P318a',
    //                'sMerchantPass2' => 'S358oP0ikj'
    //            ]
        ),
    )
);


