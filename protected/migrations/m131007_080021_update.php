<?php

class m131007_080021_update extends CDbMigration
{
	public function up()
	{
        $invites = Invite::model()->findAll("owner_id = receiver_id");
        /* @var Invite $invite */
        foreach($invites as $invite) {
            if($invite->ownerUser->profile->email !== $invite->email){
                $invite->email = $invite->ownerUser->profile->email;
                $invite->update();
            }
        }
	}

	public function down()
	{
		echo "migration down.\n";
		return true;
	}

}