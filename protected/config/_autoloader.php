<?php
/**;
 * Auto loader rules for Yii
 */
return [
    'import' => [
        /* Components  */
        'application.components.*',
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

        /* Controllers */
        'application.controllers.static.*',

        /* Extensions */
        'application.extensions.*',
        'application.extensions.PHPExcel.*',
        'application.extensions.phpmailer.*',
        'application.vendors.*',

        /* Models  */
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

        /* Modules */
        'application.modules.user.models.*',
        'application.modules.user.components.*',
        'application.modules.avatar.models.*',
        'application.modules.message.models.*',
        'application.modules.profile.models.*',
        'application.modules.registration.models.*',
        'application.modules.user.controllers.*',
        'application.modules.role.models.*',
        'application.modules.usergroup.models.*',
    ]
];