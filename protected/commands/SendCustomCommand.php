<?php

class SendCustomCommand extends CConsoleCommand
{
    public function actionIndex($email)
    {
        $emails = [];
        if($email === 'all') {
            /* @var $profile YumProfile */
            foreach(YumProfile::model()->findAll() as $profile) {
                $emails[$profile->email] = $profile->firstname;
            }
        }elseif($email === 'all_corporate') {
            /* @var $account UserAccountCorporate */
            foreach(UserAccountCorporate::model()->findAll() as $account) {
                $emails[$account->user->profile->email]=$account->user->profile->firstname;
            }
        }elseif($email === 'all_personal'){
            /* @var $account UserAccountPersonal */
            foreach(UserAccountPersonal::model()->findAll() as $account) {
                $emails[$account->user->profile->email]=$account->user->profile->firstname;
            }
        }else{
            $emails_temp = explode(',', $email);
            foreach($emails_temp as $email){
                $profile = YumProfile::model()->findByAttributes(['email'=>$email]);
                $emails[$profile->email] = $profile->firstname;
            }
        }
        $count_send = 0;
        $not_send = [];
        $not_valid = [];
        foreach($emails as $email => $name) {

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $not_valid[] = $email;
                continue;
            }
            $body = UserService::renderEmailPartial('custom_mail', [
                'name' => $name
            ]);

            $mail = array(
                'from' => Yum::module('registration')->registrationEmail,
                'to' => $email,
                'subject' => 'Новые возможности skiliks',
                'body' => $body,
                'embeddedImages' => [
                    [
                        'path'     => Yii::app()->basePath.'/assets/img/mailtopangela.png',
                        'cid'      => 'mail-top-angela',
                        'name'     => 'mailtopangela',
                        'encoding' => 'base64',
                        'type'     => 'image/png',
                    ],[
                        'path'     => Yii::app()->basePath.'/assets/img/mailanglabtm.png',
                        'cid'      => 'mail-bottom-angela',
                        'name'     => 'mailbottomangela',
                        'encoding' => 'base64',
                        'type'     => 'image/png',
                    ],[
                        'path'     => Yii::app()->basePath.'/assets/img/mail-bottom.png',
                        'cid'      => 'mail-bottom',
                        'name'     => 'mailbottom',
                        'encoding' => 'base64',
                        'type'     => 'image/png',
                    ],
                ],
            );
            $sent = MailHelper::addMailToQueue($mail);
            if($sent) {
                $count_send++;
            }else{
                $not_send[] = $email;
            }
        }

        echo "Отправлено ".$count_send."\n";
        if(!empty($not_send)){
            echo "Не отправлено ".count($not_send).' - '.implode(','. $not_send)."\n";
        }
        if(!empty($not_valid)) {
            echo "Не валидные ".count($not_valid).' - '.implode(',', $not_valid)."\n";
        }

    }

}