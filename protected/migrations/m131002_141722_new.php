<?php

class m131002_141722_new extends CDbMigration
{
	public function safeUp()
	{
        $users = YumUser::model()->findAll();
        /* @var $user YumUser */
        foreach($users as $user) {
            if($user->isAnonymous() && false === $user->isActive()){
                $user->delete();
            }
        }
	}

	public function down()
	{
		echo "migration down.\n";
		return true;;
	}

}