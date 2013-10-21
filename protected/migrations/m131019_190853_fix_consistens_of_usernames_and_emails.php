<?php

class m131019_190853_fix_consistens_of_usernames_and_emails extends CDbMigration
{
	public function up()
	{
        $users = YumUser::model()->findAll();

        foreach ($users as $user) {
            if ($user->username != substr(md5($user->profile->email), 0,20)) {
                $user->username = substr(md5($user->profile->email), 0,20);
                try {
                    $user->save(false);
                } catch(Exception $e) {
                    echo sprintf(
                        'Exception in %s for %s.'."\n",
                        $user->username,
                        $user->profile->email
                    );
                }
            }
        }
	}

	public function down()
	{
		echo "m131019_190853_fix_consistens_of_usernames_amd_emails does not support migration down.\n";
	}
}