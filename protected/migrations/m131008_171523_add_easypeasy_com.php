<?php

class m131008_171523_add_easypeasy_com extends CDbMigration
{
	public function up()
	{
        $this->insert('free_email_provider', ['domain' => 'easypeasy.com']);
	}

	public function down()
	{
		echo "m131008_171523_add_easypeasy_com does not support migration down.\n";
	}
}