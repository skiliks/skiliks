<?php

class m130327_150525_mysql_traditional_mode extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('characters', 'title', 'VARCHAR(255) NOT NULL');
	}

	public function down()
	{
		echo "m130327_150525_mysql_traditional_mode does not support migration down.\n";
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