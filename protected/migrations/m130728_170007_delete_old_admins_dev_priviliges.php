<?php

class m130728_170007_delete_old_admins_dev_priviliges extends CDbMigration
{
	public function up()
	{
        $arr = ['gugu', 'vad', 'kirill', 'ahmed', 'rkilimov'];

        $action = YumAction::model()->findByAttributes([
            'title' => 'start_dev_mode'
        ]);

        foreach ($arr as $username) {
            $user = YumUser::model()->findByAttributes([
                'username' => $username
            ]);
            if (null!==$user)
            {
                YumPermission::model()->deleteAllByAttributes([
                    'subordinate_id' => $user->id,
                    'type'           => 'user',
                    'action'         => $action->id
                ]);
            }
        }
	}

	public function down()
	{
		echo "m130728_170007_delete_old_admins_dev_priviliges does not support migration down.\n";
	}
}