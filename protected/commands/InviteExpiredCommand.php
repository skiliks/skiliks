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
        Invite::model()->findAll("status = ".Invite::STATUS_PENDING." AND sent_time");//TODO:in progress
    }
}