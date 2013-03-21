<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivan
 * Date: 21.03.13
 * Time: 23:23
 * To change this template use File | Settings | File Templates.
 */

class Test extends PHPUnit_Framework_TestCase {

    public function testRun(){


        $time = time() - Yii::app()->params['cron']['InviteExpired'];
        $invites = Invite::model()->findAll("status = ".Invite::STATUS_PENDING." AND sent_time <= ".$time);

        foreach($invites as $invite){
            $invite->inviteExpired();
        }

    }

}
