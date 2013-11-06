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
        echo "time: ".date("Y-m-d H:i:s")."\n";
        
        InviteService::inviteExpired();
        UserService::tariffExpired();
    }
}