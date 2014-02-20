<?php

class m140220_114950_update_invite_expired_deleted extends CDbMigration
{
	public function up()
	{
        /* @var $invites Invite[] */
        $invites = Invite::model()->findAllByAttributes(['status'=>4]);
        foreach($invites as $invite) {
            $invite->status = Invite::STATUS_DELETED;
            $invite->save(false);
        }
	}

	public function down()
	{
		echo "m140220_114950_update_invite_expired_deleted does not support migration down.\n";
		return false;
	}

}