<?php

class m130330_165711_phone_calls extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('phone_calls', 'dialog_code', 'VARCHAR(20)');
	}

	public function down()
	{
		echo "m130330_165711_phone_calls does not support migration down.\n";
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