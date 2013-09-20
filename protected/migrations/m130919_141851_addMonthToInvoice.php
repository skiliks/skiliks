<?php

class m130919_141851_addMonthToInvoice extends CDbMigration
{
	public function up()
	{
        $this->execute('ALTER TABLE `invoice` ADD `month_selected` INT  NULL  DEFAULT NULL  AFTER `comment`');
	}

	public function down()
	{
		echo "m130919_141851_addMonthToInvoice does not support migration down.\n";
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