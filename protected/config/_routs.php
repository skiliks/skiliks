<?php
/**
 * Router aliases
 * Все псевдонимы URL и общие правила подмены URL для Controller->Action()
 */
return [
    'components' => [
        'urlManager' => [
            'urlFormat'      => 'path',
            'showScriptName' => false,

            'rules' => [
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
                'watchVideo'                                     => 'static/site/watchVideo',
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
                'dashboard/dontShowInviteReferralsPopup' => 'static/dashboard/dontShowInviteReferralsPopup',
                'dashboard/dontShowTariffExpirePopup' => 'static/dashboard/dontShowTariffExpirePopup',
                'dashboard/switchAssessmentResultsRenderType' => 'static/dashboard/SwitchAssessmentResultsRenderType',
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
            ]
        ]
    ]
];