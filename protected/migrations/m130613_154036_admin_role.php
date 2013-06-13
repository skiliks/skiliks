<?php

class m130613_154036_admin_role extends CDbMigration
{
	public function safeUp()
	{
        $users = Yii::app()->params['initial_data']['users'];
        foreach($users as $user) {
            $user_db = YumUser::model()->findByAttributes(['username'=>$user['username']]);
            $user_db->is_admin = 1;
            $user_db->update();
            echo $user['username']." - done\r\n";
        }
	}

	public function safeDown()
	{
        $users = Yii::app()->params['initial_data']['users'];
        foreach($users as $user) {
            $user_db = YumUser::model()->findByAttributes(['username'=>$user['username']]);
            $user_db->is_admin = 0;
            $user_db->update();
            echo $user['username']." - done\r\n";
        }
	}

}