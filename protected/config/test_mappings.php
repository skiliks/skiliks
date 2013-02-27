<?php
/**
 * Example: Yii::app()->params['test_mappings']['dialogs']['key'][2]
 */
return [

    'icons' => [
        'todo' => '',
        'phone' => '',
        'mail' => "id=icons_email",
        'visit' => '',
        'documents' => ''
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
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[1]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[2]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[3]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[4]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[5]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[6]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[7]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[8]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[9]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[10]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[11]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[12]/",
        'trudyakin' => "//ul[contains(@class,'ui-autocomplete')]/li[13]/a",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[14]/",
        'krutko' => "//ul[contains(@class,'ui-autocomplete')]/li[15]/a",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[16]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[17]/",
        '' => "//ul[contains(@class,'ui-autocomplete')]/li[18]/"
    ],


    'dialogs' => [
        'key' => [
            'key1' => 'value',
        ]
    ]

];