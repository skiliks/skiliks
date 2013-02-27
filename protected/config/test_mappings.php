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


    'dialogs' => [
        'key' => [
            'key1' => 'value',
        ]
    ]

];