<?php

class m140620_122426_add_white_list extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user', 'emails_white_list', 'TEXT');

        $users = YumUser::model()->findAll();

        /** @var YumUser $user */
        foreach ($users as $user) {
            $user->emails_white_list = $user->profile->email;
            $user->save(false, ['emails_white_list']);
        }
	}

	public function down()
	{
        $this->dropColumn('user', 'emails_white_list');
	}
}