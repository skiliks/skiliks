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
        'documents' => 'id=icons_documents'
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
        'button_to_continue' => "//div[@class='mail-popup']//td[1]/div['Продолжить']",
        'send' => "xpath=(//*[@id='mailEmulatorReceivedButton']/a[contains(text(),'отправить')])",
        'close' => "css=.btn-close button",
        'plan' => "link=запланировать",
        'popup_save' => "//table[@class='mail-popup-btn']/tbody/tr/td[3]/div[@class='mail-popup-button']/div",
        'popup_unsave' => "//table[@class='mail-popup-btn']/tbody/tr/td[1]/div[@class='mail-popup-button']/div",
        'popup_cancel' => "//table[@class='mail-popup-btn']/tbody/tr/td[2]/div[@class='mail-popup-button']/div",
    ],

    'mail_main' => [
        'new_email' => "css=a.NEW_EMAIL",
        'reply_email' => "css=a.REPLY_EMAIL",
        'reply_all_email' => "css=a.REPLY_ALL_EMAIL",
        'forward_email' => "css=a.FORWARD_EMAIL",
        'add_to_plan' => "css=a.ADD_TO_PLAN",
        'delete' => "css=a.MOVE_TO_TRASH",
        'save' => "css=a.SAVE_TO_DRAFTS",
        'inbox' => "xpath=//*[@id='FOLDER_INBOX']/label",
        'draft' => "xpath=//*[@id='FOLDER_DRAFTS']/label",
        'outbox' => "xpath=//*[@id='FOLDER_SENDED']/label",
        'trash' => "xpath=//*[@id='FOLDER_TRASH']/label",
        'close' => "css=.btn-close button"
    ],

    'todo' => [
        '' => '',
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


    'documents' => [
        '' => '',
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
        'sim_points' => "link=Дополнительные таблицы для Selenium тестов (показать/скрыть)",
        'goals' => "//tr[contains(@class, 'learning-area-code-1')]/td[3]",
        'admm_positive' => "//tr[@class='matrix-points-sum-scale-type-positive']/td[2]",
        'admm_negative' => "//tr[@class='matrix-points-sum-scale-type-negative']/td[2]",
        'admm_personal' => "//tr[@class='matrix-points-sum-scale-type-personal']/td[2]",
        'tasks2'=> "//tr[contains(@class, 'learning-area-code-2')]/td[3]",
        'management3' => "//tr[contains(@class, 'learning-area-code-3')]/td[3]",
        'communication4' => "//tr[contains(@class, 'learning-area-code-4')]/td[3]",
        'calls6' => "//tr[contains(@class, 'learning-area-code-6')]/td[3]",
        'meetings7' => "//tr[contains(@class, 'learning-area-code-7')]/td[3]",
        'personal9' => "//tr[contains(@class, 'learning-area-code-9')]/td[3]",
        'personal10' => "//tr[contains(@class, 'learning-area-code-10')]/td[3]",
        'personal11' => "//tr[contains(@class, 'learning-area-code-11')]/td[3]",
        'personal12' => "//tr[contains(@class, 'learning-area-code-12')]/td[3]",
        'personal13' => "//tr[contains(@class, 'learning-area-code-13')]/td[3]",
        'personal14' => "//tr[contains(@class, 'learning-area-code-14')]/td[3]",
        'personal15' => "//tr[contains(@class, 'learning-area-code-15')]/td[3]",
        'personal16' => "//tr[contains(@class, 'learning-area-code-16')]/td[3]",
    ],


    'other' => [
        '' => '',
    ],

    //работает только для первого адресата
    'mail_contacts' => [
        'advokatov' => "//ul[contains(@class,'ui-autocomplete')]/li[1]/a",
        'blesk' => "//ul[contains(@class,'ui-autocomplete')]/li[2]/a",
        'boss' => "//ul[contains(@class,'ui-autocomplete')]/li[3]/a",
        'bobr' => "//ul[contains(@class,'ui-autocomplete')]/li[4]/a",
        'hozin' => "//ul[contains(@class,'ui-autocomplete')]/li[5]/a",
        'analitics' => "//ul[contains(@class,'ui-autocomplete')]/li[6]/a",
        'gorbatuk' => "//ul[contains(@class,'ui-autocomplete')]/li[7]/a",
        'denejnaya' => "//ul[contains(@class,'ui-autocomplete')]/li[8]/a",
        'dobrohotov' => "//ul[contains(@class,'ui-autocomplete')]/li[9]/a",
        'dolgova' => "//ul[contains(@class,'ui-autocomplete')]/li[10]/a",
        'trudyakin' => "//ul[contains(@class,'ui-autocomplete')]/li[11]/a",
        'jeleznyi' => "//ul[contains(@class,'ui-autocomplete')]/li[12]/a",
        'kamenskiy' => "//ul[contains(@class,'ui-autocomplete')]/li[13]/a",
        'krutko' => "//ul[contains(@class,'ui-autocomplete')]/li[14]/a",
        'ludovkina' => "//ul[contains(@class,'ui-autocomplete')]/li[15]/a"
    ],


    'send_message_quickly' => [
        'MS10' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[1]",
        'MS20' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[2]",
        'MS21' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[3]",
        'MS22' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[4]",
        'MS23' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[5]",
        'MS27' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[6]",
        'MS28' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[7]",
        'MS29' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[8]",
        'MS30' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[9]",
        'MS32' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[10]",
        'MS35' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[11]",
        'MS36' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[12]",
        'MS37' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[13]",
        'MS39' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[14]",
        'MS48' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[15]",
        'MS49' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[16]",
        'MS50' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[17]",
        'MS51' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[18]",
        'MS53' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[19]",
        'MS54' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[20]",
        'MS55' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[21]",
        'MS57' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[22]",
        'MS58' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[23]",
        'MS60' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[24]",
        'MS61' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[25]",
        'MS69' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[26]",
        'MS74' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[27]",
        'MS76' => "//div[1]/div[2]/div/div/div[4]/form[2]/a[28]"
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
        'F1' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][1]/tbody/tr/td[1]",
        'F10' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][1]/tbody/tr/td[2]",
        'F11' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][1]/tbody/tr/td[3]",
        'F12' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][1]/tbody/tr/td[4]",
        'F13' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][1]/tbody/tr/td[5]",
        'F14' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][1]/tbody/tr/td[6]",
        'F15' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][1]/tbody/tr/td[7]",
        'F16' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][1]/tbody/tr/td[8]",
        'F17' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][1]/tbody/tr/td[9]",
        'F19' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][1]/tbody/tr/td[10]",
        'F2' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][2]/tbody/tr/td[1]",
        'F20' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][2]/tbody/tr/td[2]",
        'F21' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][2]/tbody/tr/td[3]",
        'F22' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][2]/tbody/tr/td[4]",
        'F3' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][2]/tbody/tr/td[5]",
        'F30' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][2]/tbody/tr/td[6]",
        'F31' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][2]/tbody/tr/td[7]",
        'F4' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][2]/tbody/tr/td[8]",
        'F7' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][2]/tbody/tr/td[9]",
        'FC31' => "//form[@class='form-inline form-flags']//table[@class='table table-bordered'][2]/tbody/tr/td[10]",
        'FNA' => "xpath=//div[1]/div[2]/div/div/div[4]/form[1]/fieldset/table[3]/tbody/tr/td"
    ],


];
