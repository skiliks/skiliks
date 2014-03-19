<?php
/**
 * Example: Yii::app()->params['test_mappings']['dialogs']['key'][2]
 */
return [

    'icons' => [
        'todo' => 'id=icons_todo',
        'phone' => 'id=icons_phone',
        'mail' => "id=icons_email",
        'door' => 'id=icons_visit',
        'documents' => 'id=icons_documents',
        'settings' => 'css=.btn-window .btn-set',
        'close' => 'css=.btn-cl.win-close',
        'close1' => 'css=.btn-close > button:nth-child(1)'
    ],

    'icons_active' => [
        'plan' => 'css=.icons-panel li.plan.icon-active > span',
        'phone' => 'css=.icons-panel li.phone.icon-active > span',
        'mail' => 'css=.icons-panel li.mail.icon-active > span',
        'door' => 'css=.icons-panel li.door.icon-active > span',
        'documents' => 'css=.icons-panel li.documents.icon-active > span'
    ],

    'time' => [
        'hour' => "css=.hour",
        'minute' => "css=.minute"
    ],

    'mail' => [
        'new_letter' => "link=новое письмо",
        'to_whom' => "id=MailClient_RecipientsList",
        'add_recipient' => "//input[@type='text']",
        'del_recipient' => "css=li.tagItem",
        'add_copy_rec' => "xpath=//*[@id='MailClient_CopiesList']/li/input",
        'button_to_continue' => "xpath=//div[@class='mail-popup']//td[1]/div['Продолжить']",
        'send' => "xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])",
        'plan' => "link=запланировать",
        'popup_save' => "xpath=//table[@class='mail-popup-btn']/tbody/tr/td[3]/div[@class='mail-popup-button']/div",
        'popup_unsave' => "xpath=//table[@class='mail-popup-btn']/tbody/tr/td[1]/div[@class='mail-popup-button']/div",
        'popup_cancel' => "xpath=//table[@class='mail-popup-btn']/tbody/tr/td[2]/div[@class='mail-popup-button']/div",
    ],

    'mail_main' => [
        'new_email' => "css=.NEW_EMAIL",
        'reply_email' => "css=.REPLY_EMAIL",
        'reply_all_email' => "css=.REPLY_ALL_EMAIL",
        'forward_email' => "css=.FORWARD_EMAIL",
        'add_to_plan' => "css=.ADD_TO_PLAN",
        'delete' => "css=.MOVE_TO_TRASH",
        'save' => "css=.SAVE_TO_DRAFTS",
        'inbox' => "xpath=//*[@id='FOLDER_INBOX']/label",
        'draft' => "xpath=//*[@id='FOLDER_DRAFTS']/label",
        'outbox' => "xpath=//*[@id='FOLDER_SENDED']/label",
        'trash' => "xpath=//*[@id='FOLDER_TRASH']/label",
    ],

    'phone' => [
        'contacts_list' => "xpath=//*[@id='phoneMainScreen']/ul/li[1]",
        'missed_calls' => "xpath=//*[@id='phoneMainScreen']/ul/li[2]",
        'reply' => "xpath=//*[@id='phone_reply']",
        'no_reply' => "xpath=//*[@id='phone_no_reply']"
    ],

    'phone_contacts' => [
        'denejnaya' => "xpath=.//*[@id='contactLi_2']/table/tbody/tr/td[3]/a",
        'trutnev' => "xpath=.//*[@id='contactLi_3']/table/tbody/tr/td[3]/a",
        'krutko' => "xpath=.//*[@id='contactLi_4']/table/tbody/tr/td[3]/a",
        'boss' => "xpath=.//*[@id='contactLi_6']/table/tbody/tr/td[3]/a",
        'dolgova' => "xpath=.//*[@id='contactLi_7']/table/tbody/tr/td[3]/a",
        'skorobey' => "xpath=.//*[@id='contactLi_9']/table/tbody/tr/td[3]/a",
        'bobr' => "xpath=.//*[@id='contactLi_11']/table/tbody/tr/td[3]/a",
        'trudyakin' => "xpath=.//*[@id='contactLi_12']/table/tbody/tr/td[3]/a",
        'dobrohotov' => "xpath=.//*[@id='contactLi_25']/table/tbody/tr/td[3]/a"
    ],

    'visit' => [
        'allow' => "css=a.visitor-allow > span",
        'deny' => "css=a.visitor-deny > span"
    ],

    'dev' => [
        'clear_queue' => "css=.btn.clean-event-trigger-queue",
        'event_input' => "id=addTriggerSelect",
        'event_create' => "xpath=//div[@class='controls']//input[@value='Создать']",
        'show_logs' => "xpath=//input[@class='btn btn-simulation-stop-logs']",
        'sim_points' => "link=Дополнительные таблицы для Selenium тестов (показать/скрыть)",
    ],

    'log' => [
        'universal' => "xpath=//div[1]/div[1]/div/ul/li[1]/a",
        'mail_log' => "xpath=//div[1]/div[1]/div/ul/li[7]/a",
        'leg_actions_detail' => "xpath=//div[1]/div[1]/div/ul/li[10]/a",
        'leg_actions_aggregated' => 'xpath=//div[1]/div[1]/div/ul/li[11]/a',

        'learn-goal-112' => "//tr[contains(@class, 'learning-goal-code-112')]/td[4]",
        'learn-goal-123' => "//tr[contains(@class, 'learning-goal-code-123')]/td[4]",

        //Определение приоритетов
        'group_1_1' => "//tr[@class='learning-goal-code-group-1_1 ']/td[4]",
        //Использование планирования в течение дня
        'group_1_2' => "//tr[@class='learning-goal-code-group-1_2 ']/td[4]",
        //Правильное определение приоритетов задач при планировании
        'group_1_3' => "//tr[@class='learning-goal-code-group-1_3 ']/td[4]",
        //Выполнение задач в соответствии с приоритетами
        'group_1_4' => "//tr[@class='learning-goal-code-group-1_4 ']/td[4]",
        //Прерывание при выполнении задач
        'group_1_5' => "//tr[@class='learning-goal-code-group-1_5 ']/td[4]",
        //Использование делегирования для управления объемом
        'group_2_1' => "//tr[@class='learning-goal-code-group-2_1 ']/td[4]",
        //Управление ресурсами различной квалификации
        'group_2_2' => "//tr[@class='learning-goal-code-group-2_2 ']/td[4]",
        //Использование обратной связи
        'group_2_3' => "//tr[@class='learning-goal-code-group-2_3 ']/td[4]",
        //Оптимальное использование каналов коммуникации
        'group_3_1' => "//tr[@class='learning-goal-code-group-3_1 ']/td[4]",
        //Эффективная работа с почтой
        'group_3_2' => "//tr[@class='learning-goal-code-group-3_2 ']/td[4]",
        //Эффективная работа со звонками
        'group_3_3' => "//tr[@class='learning-goal-code-group-3_3 ']/td[4]",
        //Эффективное управление встречами
        'group_3_4' => "//tr[@class='learning-goal-code-group-3_4 ']/td[4]",

        'admm_positive' => "//tr[@class='matrix-points-sum-scale-type-positive']/td[2]",
        'admm_negative' => "//tr[@class='matrix-points-sum-scale-type-negative']/td[2]",
        'admm_personal' => "//tr[@class='matrix-points-sum-scale-type-personal']/td[2]",

        'learning_area_1' => "//tr[contains(@class, 'learning-area-code-1')]/td[3]",
        'learning_area_2'=> "//tr[contains(@class, 'learning-area-code-2')]/td[3]",
        'learning_area_3' => "//tr[contains(@class, 'learning-area-code-3')]/td[3]",
    ],

    //работает только для первого адресата
    'mail_contacts' => [
        'advokatov' => "//ul[contains(@class,'ui-autocomplete')]/li[1]/a",
        'blesk' => "//ul[contains(@class,'ui-autocomplete')]/li[2]/a",
        'bobr' => "//ul[contains(@class,'ui-autocomplete')]/li[3]/a",
        'boss' => "//ul[contains(@class,'ui-autocomplete')]/li[4]/a",
        'analitics' => "//ul[contains(@class,'ui-autocomplete')]/li[5]/a",
        'gorbatuk' => "//ul[contains(@class,'ui-autocomplete')]/li[6]/a",
        'denejnaya' => "//ul[contains(@class,'ui-autocomplete')]/li[7]/a",
        'dobrohotov' => "//ul[contains(@class,'ui-autocomplete')]/li[8]/a",
        'dolgova' => "//ul[contains(@class,'ui-autocomplete')]/li[9]/a",
        'jeleznyi' => "//ul[contains(@class,'ui-autocomplete')]/li[10]/a",
        'kamenskiy' => "//ul[contains(@class,'ui-autocomplete')]/li[11]/a",
        'krutko' => "//ul[contains(@class,'ui-autocomplete')]/li[12]/a",
        'loshadkin' => "//ul[contains(@class,'ui-autocomplete')]/li[13]/a",
        'ludovkina' => "//ul[contains(@class,'ui-autocomplete')]/li[14]/a",
        'miagkov' => "//ul[contains(@class,'ui-autocomplete')]/li[15]/a",
        'petrashevich' => "//ul[contains(@class,'ui-autocomplete')]/li[16]/a",
        'razumnui' => "//ul[contains(@class,'ui-autocomplete')]/li[17]/a",
        'skorobey' => "//ul[contains(@class,'ui-autocomplete')]/li[18]/a",
        'tochnuh' => "//ul[contains(@class,'ui-autocomplete')]/li[19]/a",
        'trudyakin' => "//ul[contains(@class,'ui-autocomplete')]/li[20]/a",
        'trutnev' => "//ul[contains(@class,'ui-autocomplete')]/li[21]/a",
        'hozin' => "//ul[contains(@class,'ui-autocomplete')]/li[22]/a",
    ],

    'set_time' => [
        'set_hours' => "xpath=//*[@id='setTimeHours']",
        'set_minutes' => "xpath=//div[@class='control-group']//input[@name='minutes']",
        'submit_time' => "xpath=//div[@class='control-group']//input[@value='Задать']",
        '0h' =>  "//button[@data-hour='0']",
        '10h' => "//button[@data-hour='10']",
        '11h' => "//button[@data-hour='11']",
        '12h' => "//button[@data-hour='12']",
        '13h' => "//button[@data-hour='13']",
        '14h' => "//button[@data-hour='14']",
        '15h' => "//button[@data-hour='15']",
        '16h' => "//button[@data-hour='16']",
        '17h' => "//button[@class='btn set-time'][9]",
        '17h50m' => "//button[@class='btn set-time'][10]"
    ],

    'flags' => [
        'F1' => "css=.F1-value",
        'F10' => "css=.F10-value",
        'F11' => "css=.F11-value",
        'F12' => "css=.F12-value",
        'F13' => "css=.F13-value",
        'F14' => "css=.F14-value",
        'F15' => "css=.F15-value",
        'F16' => "css=.F16-value",
        'F17' => "css=.F17-value",
        'F19' => "css=.F19-value",
        'F2' => "css=.F2-value",
        'F20' => "css=.F20-value",
        'F21' => "css=.F21-value",
        'F22' => "css=.F22-value",
        'F3' => "css=.F3-value",
        'F30' => "css=.F30-value",
        'F31' => "css=.F31-value",
        'F32' => "css=.F32-value",
        'F33' => "css=.F33-value",
        'F34' => "css=.F34-value",
        'F35' => "css=.F35-value",
        'F36' => "css=.F36-value",
        'F37' => "css=.F37-value",
        'F38_1' => "css=.F38_1-value",
        'F38_2' => "css=.F38_2-value",
        'F38_3' => "css=.F38_3-value",
        'F38_4' => "css=.F38_4-value",
        'F4' => "css=.F4-value",
        'F7' => "css=.F7-value",
        'FCS1' => "css=.FCS1-value",
        'FNA' => "css=.FNA-value"
    ],

    'site' => [
        'logIn' => "css=.action-sign-in",
        'logOut' => "css=.locator-log-out-link",
        'notRegDemo'=>"css=.start-lite-simulation-btn",
        'recovery' => "css=.action-password-recovery",
        'recovery_email' => "css=#YumPasswordRecoveryForm_email",
        'recovery_button' => "name=yt1",
        'change_pass' => "css=#YumUserChangePassword_password",
        'verify_pass' => "css=#YumUserChangePassword_verifyPassword",
        'save_new_pass' => "css=.us-button-submit",
        'logo_img' => "css=.locator-logo-head",
        'username' => "css=#YumUserLogin_username",
        'userpass' => "css=#YumUserLogin_password",
        'enter' => "name=yt0"
    ],

    'site_register' =>
    [
        'registration_button' => "xpath=//section/section[1]/div/span[1]/a",
        'registration_link_header' => "xpath=//*[@id='yw0']/li[3]/a",
        'registration_link_popup' => "css=.unstandard-registration-link",
        'userName' => 'css=#YumProfile_firstname',
        'userSurname' => 'css=#YumProfile_lastname',
        'userEmail' => 'css=#YumProfile_email',
        'password1' => 'css=#YumUser_password',
        'password2' => 'css=#YumUser_password_again',
        'register_button' => "xpath=//*[@id='registration-form']/div[7]/input",
        'checkbox_terms' => "xpath=//*[@id='YumUser_agree_with_terms']",
        'link_terms' => 'css=.action-show-terms-pop-up',
        'close_terms' => 'xpath=//div[4]/div[1]/a/span'
    ],

    'personal' =>
    [
        'dashboard' => "xpath=//*[@id='yw3']/li[1]/a",
        'my_profile' => "xpath=//*[@id='yw3']/li[2]/a",
        'username' => "css=.icon-profile-personal",
        'feedback' => "css=.action-feedback",
        'last_result' => "css=.action-display-simulation-details-popup",
        'change_result_view' => "css=.action-switch-assessment-results-render-type",
        'demo_sim' => "css=.action-open-lite-simulation-popup",
        'accept_invite' => "css=.action-accept-invite",
        'decline_invite' => "css=.action-decline-invite"
    ],

    'corporate' =>
    [
        'username' => "css=.profile-icon.icon-profile-corporate",
        'startLite' => "css=.action-open-lite-simulation-popup",
        'startFull' => "css=.action-open-full-simulation-popup",
        'changeResultPresentation' => "css=.assessment-results-type-switcher",
        'inviteName' => "css=#Invite_firstname",
        'inviteSurname' => "css=#Invite_lastname",
        'inviteEmail' => "css=#Invite_email",
        'addVacancy' => "css=.action-add-vacancy.button-add-vacancy",
        'sendInvite' => "css=.selenium-button-send-invite",
        'invites_limit' => "css=.selenium-simulations-amount",
        'profile'=>"xpath=//*[@id='yw3']/li[2]/a",
    ],

    'corporate_profile' =>
    [
        'name'=>"css=#profile_firstname",
        'lastname'=>"css=#profile_lastname",
        'email'=>"xpath=//*[@id='account-corporate-personal-form']/div[2]/span"
    ],

    'popup_send_invite' =>  /// ничего еще не меняла из локаторов
    [
        'fullName' => "css=#Invite_fullname",
        'send' => "css=input[name='send']",
        //TODO: Дополнить маппинги для тексовых полей, чекбокса, скролов и т.д.
    ],

    'register_by_link' =>
    [
        'invite_name' => "css=#YumProfile_firstname",
        'invite_surname' => "css=#YumProfile_lastname",
        'password' => "css=#YumUser_password",
        'password_again' => "css=#YumUser_password_again",
        'checkbox_terms' => "css=#YumUser_agree_with_terms",
        'link_terms' => "css=.action-show-terms-pop-up",
        'close_terms_popup' => "xpath=//div[3]/div[1]/a/span",  // локально будет 4й див
        'register_button' => "xpath=//*[@id='registration-by-link-form']/div[6]/input",
        'decline_register' => "css=.action-decline-invite",
        'decline_reason_0' => "css=#DeclineExplanation_reason_id_0",
        'decline_reason_1' => "css=#DeclineExplanation_reason_id_1",
        'decline_reason_2' => "css=#DeclineExplanation_reason_id_2",
        'decline_reason_3' => "css=#DeclineExplanation_reason_id_3",
        'close_decline_popup' => "xpath=//div[3]/div[1]/a/span", // локально будет 6й див, потому что локально есть еще и сайтхарт
        'back_to_registration' => "css=.action-close-popup",
        'confirm_decline_invite' => "css=.action-confirm-decline",
        'input_area_for_reason' => "css=#DeclineExplanation_description"
    ],

    'user_auth' =>
    [
        'email' => "xpath=//section/div/form/div//..//*[@id='YumUserLogin_username']",
        'password'=>"xpath=//section/div/form/div//..//*[@id='YumUserLogin_password']",
        'login' => "xpath=//section/section/div/form/div/div[1]/input[3]"
    ],

    'site_urls' =>
    [
        'en-ru' => "/ru",
        'ru-en' => "/en",
        'home' => "/",
        'about_us'=>"/static/team",
        'about_product' => "/static/product",
        'tariffs'=>"/static/tariffs",
        'registration' => "/registration/single-account",
        'old_registration' => "/registration",
        'help'=>"/help/general",
        '404' => "/404",
        'user_auth'=>"/user/auth",
        'after_registration' => "/userAuth/afterRegistration",
    ],

];
