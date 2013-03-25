<?php
/**
 * Created by JetBrains PhpStorm.
 * User: PS
 * Date: 3/13/13
 * Time: 6:42 PM
 * To change this template use File | Settings | File Templates.
 */

class InviteExpiredCommand extends CConsoleCommand
{
    public function actionIndex() // 7 days
    {
        $time = time() - Yii::app()->params['cron']['InviteExpired'];
        /** @var $invites Invite[] */
        $invites = Invite::model()->findAll("status = ".Invite::STATUS_PENDING." AND sent_time <= ".$time);

        foreach($invites as $invite){
            $invite->inviteExpired();
        }
    }
}