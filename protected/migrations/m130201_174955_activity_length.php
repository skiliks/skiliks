<?php

class m130201_174955_activity_length extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('activity', 'id', 'string NOT NULL');
	}

	public function down()
	{
		echo "m130201_174955_activity_length does not support migration down.\n";
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