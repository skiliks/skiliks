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
        /** @var $invites Invite[] */
        $invites = Invite::model()->findAll(
            sprintf("status IN ('%s', '%s', '%s') AND sent_time <= '%s' ",
                Invite::STATUS_PENDING,
                Invite::STATUS_ACCEPTED,
                Invite::STATUS_IN_PROGRESS,
                $time
            ));

        foreach($invites as $invite){
            $invite->inviteExpired();
        }

        /* @var $users UserAccountCorporate[] */
        $accounts = UserAccountCorporate::model()->findAll("tariff_expired_at <= '".(new DateTime())->format("Y-m-d H:i:s")."'");
        if(null !== $accounts){
            /* @var $user UserAccountCorporate */
            foreach($accounts as $account){
                $initValue = $account->invites_limit;

                $account->invites_limit = 0;
                $account->update();

                UserService::logCorporateInviteMovementAdd('InviteExpiredCommand', $account, $initValue);
            }
        }

    }
}