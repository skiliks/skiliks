<?php

class m130923_092936_payment_paid_date_changes extends CDbMigration
{
	public function up()
	{
        $this->execute('ALTER TABLE `invoice` CHANGE `paid_date` `paid_at` DATETIME  NULL  DEFAULT NULL');
	}

	public function down()
	{
		echo "m130923_092936_payment_paid_date_changes does not support migration down.\n";
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