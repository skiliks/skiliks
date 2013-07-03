<?php

class m130703_112501_remove extends CDbMigration
{
	public function up()
	{
        $this->dropTable("mail_settings");
	}

	public function down()
	{

		echo "m130703_112501_remove does not support migration down.\n";
		return false;
	}

}