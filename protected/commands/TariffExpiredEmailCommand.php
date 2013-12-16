<?php

class TariffExpiredEmailCommand extends CConsoleCommand {

    public function actionIndex(){

        $date = new DateTime();
        $date->add(new DateInterval('P3D'));
        $date_expire_from = $date->format('Y-m-d 00:00:00');
        $date->add(new DateInterval('P1D'));
        $date_expire_to = $date->format('Y-m-d 00:00:00');

        $criteria = new CDbCriteria();
        $criteria->addCondition("account_corporate.tariff_expired_at >= '". $date_expire_from .
                                "' AND account_corporate.tariff_expired_at < '" . $date_expire_to. "'");

        $users = YumUser::model()->with("account_corporate")->findAll($criteria);

        if(!empty($users)) {

            foreach($users as $user) {

                if($user->account_corporate->getTotalAvailableInvitesLimit() > 0) {

                    $emailTemplate = Yii::app()->params['emails']['tariffExpiredTemplate'];

                }else{

                    continue;

                }

                $path = Yii::getPathOfAlias('application.views.global_partials.mails').'/'.$emailTemplate.'.php';

                $body = $this->renderFile($path, [
                    'user' => $user
                ], true);

                $mail = new SiteEmailOptions();
                $mail->from        = 'support@skiliks.com';
                $mail->to          = $user->profile->email;
                $mail->subject     = 'Неиспользованные симуляции на skiliks.com';
                $mail->body        = $body;
                $mail->embeddedImages = [
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
                    ];

                try {
                    MailHelper::addMailToQueue($mail);
                    echo $user->profile->email."\n";
                } catch (phpmailerException $e) {
                    echo $e;
                }

            }
        }
    }
}