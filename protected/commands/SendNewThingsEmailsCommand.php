<?php

class SendNewThingsEmailsCommand extends CConsoleCommand
{
    public function actionIndex()
    {

        $accounts = UserAccountCorporate::model()->findAll();


        foreach($accounts as $account) {

            $emailTemplate = Yii::app()->params['emails']['newThingsInProject'];

            $path = Yii::getPathOfAlias('application.views.global_partials.mails').'/'.$emailTemplate.'.php';

            $body = $this->renderFile($path, [], true);

            $mail = [
                'from'        => 'support@skiliks.com',
                'to'          => $account->user->profile->email,
                'subject'     => 'ВАЖНО! Изменения и возможности в новой версии skiliks.com',
                'body'        => $body,
                'embeddedImages' => [
                    [
                        'path'     => Yii::app()->basePath.'/assets/img/mail-top.png',
                        'cid'      => 'mail-top',
                        'name'     => 'mailtop',
                        'encoding' => 'base64',
                        'type'     => 'image/png',
                    ],[
                        'path'     => Yii::app()->basePath.'/assets/img/mail-top-2.png',
                        'cid'      => 'mail-top-2',
                        'name'     => 'mailtop2',
                        'encoding' => 'base64',
                        'type'     => 'image/png',
                    ],[
                        'path'     => Yii::app()->basePath.'/assets/img/mail-right-1.png',
                        'cid'      => 'mail-right-1',
                        'name'     => 'mailright1',
                        'encoding' => 'base64',
                        'type'     => 'image/png',
                    ],[
                        'path'     => Yii::app()->basePath.'/assets/img/mail-right-2.png',
                        'cid'      => 'mail-right-2',
                        'name'     => 'mailright2',
                        'encoding' => 'base64',
                        'type'     => 'image/png',
                    ],[
                        'path'     => Yii::app()->basePath.'/assets/img/mail-right-3.png',
                        'cid'      => 'mail-right-3',
                        'name'     => 'mailright3',
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
            ];

            try {
                MailHelper::addMailToQueue($mail);
                echo $account->user->profile->email."\n";
            } catch (phpmailerException $e) {
                echo $e;
            }
        }
    }

}