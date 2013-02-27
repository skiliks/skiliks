<?php

class m130226_150251_user_unique_email extends CDbMigration
{
	public function up()
	{
        $this->createIndex('user_email_unique', 'users', 'email', true);
	}

	public function down()
	{
		echo "m130226_150251_user_unique_email does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}