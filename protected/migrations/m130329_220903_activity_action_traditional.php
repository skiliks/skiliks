<?php

class m130329_220903_activity_action_traditional extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('activity', 'grandparent', 'string NOT NULL');
	}

	public function down()
	{
		echo "m130329_220903_activity_action_traditional does not support migration down.\n";
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