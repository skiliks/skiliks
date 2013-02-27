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
        '' => '',
    ],


    'visit' => [
        '' => '',
    ],


    'documents' => [
        '' => '',
    ],


    'dev' => [
        '' => '',
    ],


    'other' => [
        '' => '',
    ],


    'phone_contacts' => [
    ],


    'mail_contacts' => [
        'advokatov' => "//ul[contains(@class,'ui-autocomplete')]/li[1]/",
        'blesk' => "//ul[contains(@class,'ui-autocomplete')]/li[2]/",
        'jukova' => "//ul[contains(@class,'ui-autocomplete')]/li[3]/",
        'serkov' => "//ul[contains(@class,'ui-autocomplete')]/li[4]/",
        'boss' => "//ul[contains(@class,'ui-autocomplete')]/li[5]/",
        'bobr' => "//ul[contains(@class,'ui-autocomplete')]/li[6]/",
        'hozin' => "//ul[contains(@class,'ui-autocomplete')]/li[7]/",
        'vasilyev' => "//ul[contains(@class,'ui-autocomplete')]/li[8]/",
        'gorbatuk' => "//ul[contains(@class,'ui-autocomplete')]/li[9]/",
        'denejnaya' => "//ul[contains(@class,'ui-autocomplete')]/li[10]/",
        'dobrohotov' => "//ul[contains(@class,'ui-autocomplete')]/li[11]/",
        'dolgova' => "//ul[contains(@class,'ui-autocomplete')]/li[12]/",
        'trudyakin' => "//ul[contains(@class,'ui-autocomplete')]/li[13]/a",
        'jeleznyi' => "//ul[contains(@class,'ui-autocomplete')]/li[14]/",
        'kamenskiy' => "//ul[contains(@class,'ui-autocomplete')]/li[15]/a",
        'krutko' => "//ul[contains(@class,'ui-autocomplete')]/li[16]/",
        'loshadkin' => "//ul[contains(@class,'ui-autocomplete')]/li[17]/",
        'lubimaya' => "//ul[contains(@class,'ui-autocomplete')]/li[18]/"
    ],


    'dialogs' => [
        'key' => [
            'key1' => 'value',
        ]
    ]

];