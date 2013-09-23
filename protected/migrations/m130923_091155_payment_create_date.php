<?php

class m130923_091155_payment_create_date extends CDbMigration
{
	public function up()
	{
        $this->execute('ALTER TABLE `invoice` CHANGE `create_date` `created_at` DATETIME  NULL  DEFAULT NULL');

	}

	public function down()
	{
		echo "m130923_091155_payment_create_date does not support migration down.\n";
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