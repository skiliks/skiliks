<?php

class m130924_102943_disabling extends CDbMigration
{
	public function up()
	{
        $this->execute("UPDATE `tariff` SET `is_free` = '0' WHERE `id` = '1'");
	}

	public function down()
	{
		$this->execute("UPDATE `tariff` SET `is_free` = '1' WHERE `id` = '1'");
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