<?php

class m130728_091814_remove_old_admins extends CDbMigration
{
	public function up()
	{
        $arr = ['gugu', 'vad', 'kirill', 'ahmed', 'rkilimov'];

        foreach ($arr as $username) {
            $this->update(
                'user',
                ['is_admin' => 0],
                "username = '".$username."'"
            );
        }
	}

	public function down()
	{
        $arr = ['gugu', 'vad', 'kirill', 'ahmed', 'rkilimov'];

        foreach ($arr as $username) {
            $this->update(
                'user',
                ['is_admin' => 0],
                "username = '".$username."'"
            );
        }
	}
}