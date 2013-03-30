<?php

class m130330_003600_learning_goal_primary_key extends CDbMigration
{
	public function up()
	{
        $this->dropIndex('PRIMARY', 'learning_goal');
        $this->addColumn('learning_goal', 'id', 'pk');
        $this->createIndex('learning_goal_uniq', 'learning_goal', 'code, scenario_id', true);
	}

	public function down()
	{
		echo "m130330_003600_learning_goal_primary_key does not support migration down.\n";
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