<?php
/**
 * Created by JetBrains PhpStorm.
 * User: PS
 * Date: 3/13/13
 * Time: 6:42 PM
 * To change this template use File | Settings | File Templates.
 */
/**
 * Что она делает?
 */
class InviteExpiredCommand extends CConsoleCommand
{
    public function actionIndex() // 7 days
    {
        //Invites
        $time = time() - Yii::app()->params['cron']['InviteExpired'];

        echo "time: ".$time."\n";
        /** @var $invites Invite[] */
        $invites = Invite::model()->findAll(
            sprintf("status IN ('%s', '%s', '%s') AND sent_time <= '%s' AND (owner_id != receiver_id OR receiver_id is NULL) ",
                Invite::STATUS_PENDING,
                Invite::STATUS_ACCEPTED,
                Invite::STATUS_IN_PROGRESS,
                $time
            ));

        foreach($invites as $invite){
            if ($invite->inviteExpired()) {
                echo sprintf("%s mark as expired \n", $invite->id);
            }
        }

        /* @var $users UserAccountCorporate[] */
        $accounts = UserAccountCorporate::model()->findAll(
            sprintf("'%s' < tariff_expired_at AND tariff_expired_at <= '%s'",
                date("Y-m-d 00:00:00"),
                date("Y-m-d 23:23:59")
        ));
        if(null !== $accounts){
            /* @var $user UserAccountCorporate */
            foreach($accounts as $account) {
                $account->is_display_tariff_expire_pop_up = 1;
                if((int)$account->invites_limit !== 0) {
                    $initValue = $account->getTotalAvailableInvitesLimit();

                    $account->invites_limit = 0;
                    $account->update();

                    $emailTemplate = Yii::app()->params['emails']['tariffExpiredTemplateIfInvitesZero'];

                    $path = Yii::getPathOfAlias('application.views.global_partials.mails').'/'.$emailTemplate.'.php';

                    $body = $this->renderFile($path, [
                        'user' => $account->user
                    ], true);

                    $mail = [
                        'from'        => 'support@skiliks.com',
                        'to'          => $account->user->profile->email,
                        'subject'     => 'Истёк тарифный план',
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

                    UserService::logCorporateInviteMovementAdd('InviteExpiredCommand', $account, $initValue);
                }
            }
        }

    }
}