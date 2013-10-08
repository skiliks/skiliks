<?php

class m131008_204424_mail_lower extends CDbMigration
{
	public function safeUp()
	{
        $profiles = YumProfile::model()->findAll();
        /* @var $profile YumProfile */
        foreach($profiles as $profile){
            $profile->email = strtolower($profile->email);
            $profile->update();
        }

        $invites = Invite::model()->findAll();
        /* @var $invite Invite */
        foreach($invites as $invite){
            $invite->email = strtolower($invite->email);
            $invite->update();
        }
	}

	public function down()
	{
		echo "migration down.\n";
		return true;
	}

}