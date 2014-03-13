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
                'team/<_lang:\w+>'           => 'static/pages/team', // sentry показывает что есть запросы к такому URL
                'static/product/<_lang:\w+>' => 'static/pages/product',
                'static/tariffs/<_lang:\w+>' => 'static/pages/tariffs',
                'static/terms'               => 'static/pages/terms',
                'static/feedback'            => 'static/pages/feedback',

                'static/drag-and-drop'       => 'static/pages/dragAndDropPrototype',

                'static/team/'    => 'static/pages/team',
                'team/'           => 'static/pages/team', // sentry показывает что есть запросы к такому URL
                'static/product/' => 'static/pages/product',
                'static/tariffs/' => 'static/pages/tariffs',

                'static/charts' => 'static/pages/charts',

                'subscription/add'       => 'static/pages/addUserSubscription',
                'static/pages/addUserSubscription' => 'static/pages/addUserSubscription', // strange, but works in this way only

                'static/comingSoonSuccess' => 'static/pages/comingSoonSuccess',
                'static/comingSoonSuccess/<_lang:\w+>' => 'static/pages/comingSoonSuccess',

                'static/<_lang:\w+>'         => 'static/pages/index',

                // SimStop and show logs
                'admin/displayLog/<simulation:\w+>' => 'static/simStopAndShowLogs/displayLog',

                // Save logs to excel on page "SimStop and show logs"
                'static/admin/saveLog/<simulation:\w+>' => 'static/simStopAndShowLogs/saveLog',

//                'admin/'                  => 'static/admin/',
//                'Admin/Log'              => 'static/Admin/Log',

                'cheat/quick-start/full'                => 'static/cheats/startSimulationForFastSeleniumTest',
                'cheat/clean-events-queue/<simId:\w+>'  => 'static/cheats/cleanEventsQueue',

                'logout'                  => 'static/userAuth/logout',

                'system-mismatch' => 'static/pages/systemMismatch',

                'profile/without-account' => 'static/site/runSimulationOrChooseAccount',

                'simulation/start'        => 'simulation/start',
                'simulation/stop'         => 'simulation/stop',
                'simulation/changeTime'   => 'simulation/changeTime',
                'simulation/startPause'   => 'simulation/startPause',
                'simulation/stopPause'    => 'simulation/stopPause',
                'simulation/exit'         => 'static/site/Exit',
                'simulation/demo'         => 'static/site/demo',
                'simulation/legacy/<mode:\w+>/<type:\w+>/<invite_id:\d+>' => 'static/pages/legacyAndTerms',

                'simulation/<id:\w+>/details'                      => 'static/dashboard/simulationDetails',
                'simulation/<mode:\w+>/<type:\w+>/<invite_id:\d+>' => 'static/site/simulation',
                'simulation/<mode:\w+>/<type:\w+>'                 => 'static/site/simulation',
                'promo/<mode:\w+>/<type:\w+>'                      => 'static/dashboard/index',

                'registration'                                   => 'static/userAuth/registration',
                'registration/by-link/<code:\w+>'                => 'static/userAuth/registerByLink',
                'registration/single-account'                    => 'static/userAuth/registrationSingleAccount',
//                'registration/account-type/added'                => 'static/userAuth/accountTypeSavesSuccessfully',
                'simulationIsStarted'                            => 'static/site/IsStarted',
//                'watchVideo'                                     => 'static/site/watchVideo',
//                'watchVideo/<_lang:\w+>'                         => 'static/site/watchVideo',
                'userStartSecondSimulation'                      => 'static/site/UserStartSecondSimulation',
                'userRejectStartSecondSimulation'                => 'static/site/UserRejectStartSecondSimulation',
                'logout/registration'                            =>'static/userAuth/LogoutAndRegistration',


                'static/break-simulations-for-self-to-self-invites' =>'static/site/breakSimulationsForSelfToSelfInvites',

                'recovery' => 'static/userAuth/recovery',

                'results'                   => 'static/userAuth/results',
                'simulation/results'        => 'static/userAuth/results', // just to handle JS 'Sim stop'
                'site/results'              => 'static/userAuth/results',

                'userAuth/<action:\w+>'     => 'static/userAuth/<action>',
                'registration/<action:\w+>' => 'static/userAuth/<action>',

                'help/general'                       => 'static/help/general',
                'help/corporate'                     => 'static/help/corporate',
                'help/personal'                      => 'static/help/personal',
                'form-errors-standard'               => 'static/pages/formErrorsStandard',
                'payment/doCashPayment'              => 'static/payment/doCashPayment',
                'payment/getRobokassaForm'           => 'static/payment/getRobokassaForm',
                'payment/success'                    => 'static/payment/success',
                'payment/fail'                       => 'static/payment/fail',
                'payment/result'                     => 'static/payment/result',
                'payment/invoiceSuccess'              => 'static/payment/InvoiceSuccess',

                'dashboard/'          => 'static/dashboard/index',
                'dashboard/corporate' => 'static/dashboard/corporate',
                'dashboard/personal'  => 'static/dashboard/personal',
                'dashboard/switchAssessmentResultsRenderType' => 'static/dashboard/SwitchAssessmentResultsRenderType',

                'profile/personal/personal-data/'  => 'static/profile/personalPersonalData',
                'profile/corporate/personal-data/' => 'static/profile/corporatePersonalData',
                'profile/corporate/password/' => 'static/profile/corporatePassword',
                'profile/personal/password/'  => 'static/profile/personalPassword',
                'profile/corporate/company-info/' => 'static/profile/corporateCompanyInfo',
                'profile/corporate/vacancies/'  => 'static/profile/corporateVacancies',
                'profile/corporate/payment-method/'  => 'static/profile/corporatePaymentMethod',
                'profile/restore-authorization/'  => 'static/profile/RestoreAuthorization',

                'vacancy/add'                     => 'static/profile/vacancyAdd',

                'profile/getSpecialization'       => 'static/profile/getSpecialization',
                'profile/corporate/vacancy/<id:\w+>/remove' => 'static/profile/removeVacancy',
                'profile/corporate/vacancy/<id:\w+>/edit' => 'static/profile/corporateVacancies',

                'profile/'          => 'static/profile/index',
                'profile/corporate' => 'static/profile/index',
                'profile/personal'  => 'static/profile/index',
                'profile/save-analytic-file-2'  => 'static/profile/SaveAssessmentAnalyticFile2',

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

                'payment/order'  => 'static/payment/order',
                'statistics/phpUnitTests' => 'statistics/statistics/phpUnitTests',
                'statistics/SeleniumTests' => 'statistics/statistics/SeleniumTests',
                'statistics/SeleniumTestsAuth' => 'statistics/statistics/SeleniumTestsAuth',
                'statistics/CiTests' => 'statistics/statistics/CiTests',
                'statistics/OrderCount' => 'statistics/statistics/OrderCount',
                'statistics/FeedbackCount' => 'statistics/statistics/FeedbackCount',

                'pdf/simulation-detail-pdf/<sim_id:\w+>/<assessment_version:\w+>' => 'PDF/SimulationDetailPDF',

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
                'admin_area/not-corporate-emails'  => 'admin_area/AdminPages/NotCorporateEmails',
                'admin_area/RegistrationList'      => 'admin_area/AdminPages/RegistrationList',
                'admin_area/change_security_risk'    => 'admin_area/AdminPages/ChangeSecurityRisk',
                'admin_area/site-log-authorization'  => 'admin_area/AdminPages/SiteLogAuthorization',
                'admin_area/site-log-account-action' => 'admin_area/AdminPages/SiteLogAccountAction',
                'admin_area/user-bruteforce'         => 'admin_area/AdminPages/UserBruteforce',
                'admin_area/admins-list'             => 'admin_area/AdminPages/AdminsList',

                'admin_area/simulations/rating/csv'                       => 'admin_area/AdminPages/SimulationsRatingCsv',
                'admin_area/simulations/rating'                           => 'admin_area/AdminPages/SimulationsRating',
                'admin_area/export-all-corporate-account-xlsx'            => 'admin_area/AdminPages/ExportAllCorporateUserXLSX',
                'admin_area/email/<id:\w+>/text'                          => 'admin_area/AdminPages/EmailText',
                'admin_area/import-scenario/<slug:\w+>/<logImportId:\w+>' => 'admin_area/AdminPages/StartImport',
                'admin_area/import-log/<id:\w+>/get-text'                 => 'admin_area/AdminPages/GetImportLog',
                'admin_area/statistics/testAuth'                          => 'admin_area/AdminPages/TestAuth',
                'admin_area/statistics/statistic-order-count'             => 'admin_area/AdminPages/StatisticOrderCount',
                'admin_area/statistics/statistic-feedback-count'          => 'admin_area/AdminPages/StatisticFeedbackCount',
                'admin_area/statistics/user-blocked-authorization'        => 'admin_area/AdminPages/StatisticUserBlockedAuthorization',
                'admin_area/users_managament/blocked-authorization-list'  => 'admin_area/AdminPages/UserBlockedAuthorizationList',

                'admin_area/statistics/statistic-mail'                    => 'admin_area/AdminPages/StatisticMail',
                'admin_area/statistics/free-disk-space'                   => 'admin_area/AdminPages/StatisticFreeDiskSpace',
                'admin_area/invite/<inviteId:\w+>/switch-can-be-reloaded' => 'admin_area/AdminPages/InviteSwitchCanBeReloaded',
                'admin_area/ban_user/<userId:\d+>'                        => 'admin_area/AdminPages/BanUser',
                'admin_area/user/<userId:\d+>/send-invites'               => 'admin_area/AdminPages/SendInvites',

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
                'admin_area/user/by-email'                => 'admin_area/AdminPages/UserDetailsByEmail',
                'admin_area/login/ghost/<userId:\d+>'             => 'admin_area/AdminPages/GhostLogin',

                'admin_area/user/<userId:\w+>/set-invites-limit/<value:[\w\-]+>' => 'admin_area/AdminPages/UserAddRemoveInvitations',


                'admin_area/corporate-account/<id:\w+>/invite-limit-logs' => 'admin_area/AdminPages/CorporateAccountInviteLimitLogs',
                'admin_area/site-user/<userId:\w+>/update-password'           => 'admin_area/AdminPages/UpdatePassword',
                'debug/cluster-data' => 'Debug/ClusterData',
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