<?php
/**
 * Example: Yii::app()->params['test_mappings']['dialogs']['key'][2]
 */
return [

    'icons' => [
        'todo' => 'id=icons_todo',
        'phone' => 'id=icons_phone',
        'mail' => "id=icons_email",
        'visit' => 'id=icons_visit',
        'documents' => 'id=icons_documents',
        'settings' => 'css=.btn-window .btn-set',
        'close' => 'css=.btn-cl.win-close',
        'close1' => 'css=.btn-close > button:nth-child(1)'
    ],

    'active_icons'=>[
        'active_plan' => '',
        'active_phone' => 'css=.phone.icon-active',
        'active_mail' => 'css=.mail.icon-active',
        'active_visit' => 'css=.door.icon-active',
        'active_documents' => '',
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
        'button_to_continue' => "//div[@class='mail-popup']//td[1]/div['Продолжить']",
        'send' => "xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])",
        'plan' => "link=запланировать",
        'popup_save' => "//table[@class='mail-popup-btn']/tbody/tr/td[3]/div[@class='mail-popup-button']/div",
        'popup_unsave' => "//table[@class='mail-popup-btn']/tbody/tr/td[1]/div[@class='mail-popup-button']/div",
        'popup_cancel' => "//table[@class='mail-popup-btn']/tbody/tr/td[2]/div[@class='mail-popup-button']/div",
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
        'contacts_list' => "//*[@id='phoneMainScreen']/ul/li[1]",
        'missed_calls' => "//*[@id='phoneMainScreen']/ul/li[2]",
        'reply' => "//a[@id='phone_reply']",
        'no_reply' => "//a[@id='phone_no_reply']"
    ],

    'phone_contacts' => [
        'denejnaya' => "xpath=(//a[contains(text(),'Позвонить')])[1]",
        'trutnev' => "xpath=(//a[contains(text(),'Позвонить')])[2]",
        'krutko' => "xpath=(//a[contains(text(),'Позвонить')])[3]",
        'loshadkin' => "xpath=(//a[contains(text(),'Позвонить')])[4]",
        'boss' => "xpath=(//a[contains(text(),'Позвонить')])[5]",
        'dolgova' => "xpath=(//a[contains(text(),'Позвонить')])[6]",
        'razumnui' => "xpath=(//a[contains(text(),'Позвонить')])[7]",
        'skorobey' => "xpath=(//a[contains(text(),'Позвонить')])[8]",
        'jeleznyi' => "xpath=(//a[contains(text(),'Позвонить')])[9]",
        'bobr' => "xpath=(//a[contains(text(),'Позвонить')])[10]",
        'trudyakin' => "xpath=(//a[contains(text(),'Позвонить')])[11]",
        'ludovkina' => "xpath=(//a[contains(text(),'Позвонить')])[12]",
        'hozin' => "xpath=(//a[contains(text(),'Позвонить')])[13]",
        'tochnuh' => "xpath=(//a[contains(text(),'Позвонить')])[14]",
        'semenova' => "xpath=(//a[contains(text(),'Позвонить')])[15]",
        'jukova' => "xpath=(//a[contains(text(),'Позвонить')])[16]",
        'advokatov' => "xpath=(//a[contains(text(),'Позвонить')])[17]",
        'golts' => "xpath=(//a[contains(text(),'Позвонить')])[18]",
        'kamenskiy' => "xpath=(//a[contains(text(),'Позвонить')])[19]",
        'vasilyev' => "xpath=(//a[contains(text(),'Позвонить')])[20]",
        'myagkov' => "xpath=(//a[contains(text(),'Позвонить')])[21]",
        'petrashevich' => "xpath=(//a[contains(text(),'Позвонить')])[22]",
        'serkov' => "xpath=(//a[contains(text(),'Позвонить')])[23]",
        'dobrohotov' => "xpath=(//a[contains(text(),'Позвонить')])[24]",
        'blesk' => "xpath=(//a[contains(text(),'Позвонить')])[25]",
        'lubimaya' => "xpath=(//a[contains(text(),'Позвонить')])[26]",
        'pogodkin' => "xpath=(//a[contains(text(),'Позвонить')])[27]"
    ],

    'visit' => [
        'allow' => "//a[@class='visitor-allow']",
        'deny' => "//a[@class='visitor-deny']"
    ],

    'dev' => [
        'event_input' => "id=addTriggerSelect",
        'event_create' => "//div[@class='controls']//input[@value='Создать']",
        'show_logs' => "//input[@class='btn btn-simulation-stop-logs']",
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
        //Завершение начатых задач
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
        'set_hours' => "//*[@id='setTimeHours']",
        'set_minutes' => "//div[@class='control-group']//input[@name='minutes']",
        'submit_time' => "//div[@class='control-group']//input[@value='Задать']",
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
];
