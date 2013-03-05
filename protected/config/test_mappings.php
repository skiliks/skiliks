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
        'hour' => "//*[@id='canvas']/ul/li[1]/span[1]",
        'minute' => "//*[@id='canvas']/ul/li[1]/span[3]"
    ],

    'mail' => [
        'new_letter' => "link=новое письмо",
        'to_whom' => "id=MailClient_RecipientsList",
        'add_recipient' => "//input[@type='text']",
        'del_recipient' => "css=li.tagItem",
        'button_to_continue' => "//div[@class='mail-popup']//td[1]/div['Продолжить']",
        'popup' => "",
    ],


    'todo' => [
        '' => '',
    ],


    'phone' => [
        'contacts_list' => "//div[@id='phoneMainScreen']/ul/li[1]",
        'missed_calls' => "//div[@id='phoneMainScreen']/ul/li[1]",
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
        'event_create' => "css=input.btn.btn-primary",
        'show_logs' => "//input[@class='btn btn-simulation-stop-logs']",
        'positive_evaluation' => "",
        'negative_evaluation' => "",
        'personal_evaluation' => ""
    ],


    'other' => [
        '' => '',
    ],

    //работает только для первого адресата
    'mail_contacts' => [
        'advokatov' => "//ul[contains(@class,'ui-autocomplete')]/li[1]/a",
        'blesk' => "//ul[contains(@class,'ui-autocomplete')]/li[2]/a",
        'jukova' => "//ul[contains(@class,'ui-autocomplete')]/li[3]/a",
        'serkov' => "//ul[contains(@class,'ui-autocomplete')]/li[4]/a",
        'boss' => "//ul[contains(@class,'ui-autocomplete')]/li[5]/a",
        'bobr' => "//ul[contains(@class,'ui-autocomplete')]/li[6]/a",
        'hozin' => "//ul[contains(@class,'ui-autocomplete')]/li[7]/a",
        'vasilyev' => "//ul[contains(@class,'ui-autocomplete')]/li[8]/a",
        'gorbatuk' => "//ul[contains(@class,'ui-autocomplete')]/li[9]/a",
        'denejnaya' => "//ul[contains(@class,'ui-autocomplete')]/li[10]/a",
        'dobrohotov' => "//ul[contains(@class,'ui-autocomplete')]/li[11]/a",
        'dolgova' => "//ul[contains(@class,'ui-autocomplete')]/li[12]/a",
        'trudyakin' => "//ul[contains(@class,'ui-autocomplete')]/li[13]/a",
        'jeleznyi' => "//ul[contains(@class,'ui-autocomplete')]/li[14]/a",
        'kamenskiy' => "//ul[contains(@class,'ui-autocomplete')]/li[15]/a",
        'krutko' => "//ul[contains(@class,'ui-autocomplete')]/li[16]/a",
        'loshadkin' => "//ul[contains(@class,'ui-autocomplete')]/li[17]/a",
        'lubimaya' => "//ul[contains(@class,'ui-autocomplete')]/li[18]/a",
        'ludovkina' => "//ul[contains(@class,'ui-autocomplete')]/li[19]/a"
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
        'set_minutes' => "//div[1]/div[2]/div/div/div[3]/form/fieldset/div[1]/div/input[2]",
        'submit_time' => "//div[1]/div[2]/div/div/div[3]/form/fieldset/div[1]/div/input[3]",
        '0h' => '//div[1]/div[2]/div/div/div[3]/form/fieldset/div[2]/button[1]',
        '10h' => '//div[1]/div[2]/div/div/div[3]/form/fieldset/div[2]/button[2]',
        '11h' => '//div[1]/div[2]/div/div/div[3]/form/fieldset/div[2]/button[3]',
        '12h' => '//div[1]/div[2]/div/div/div[3]/form/fieldset/div[2]/button[4]',
        '13h' => '//div[1]/div[2]/div/div/div[3]/form/fieldset/div[2]/button[5]',
        '14h' => '//div[1]/div[2]/div/div/div[3]/form/fieldset/div[2]/button[6]',
        '15h' => '//div[1]/div[2]/div/div/div[3]/form/fieldset/div[2]/button[7]',
        '16h' => '//div[1]/div[2]/div/div/div[3]/form/fieldset/div[2]/button[8]',
        '17h' => '//div[1]/div[2]/div/div/div[3]/form/fieldset/div[2]/button[9]',
        '17h50m' => '//div[1]/div[2]/div/div/div[3]/form/fieldset/div[2]/button[10]'
    ],


];