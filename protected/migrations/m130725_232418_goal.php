<?php

class m130725_232418_goal extends CDbMigration
{
	public function up()
	{
        $this->addColumn('learning_goal', 'learning_goal_group_id', 'int(11) NOT NULL');
        //$this->addForeignKey('fk_learning_goal_learning_goal_group_id', 'learning_goal', 'learning_goal_group_id', 'learning_goal_group', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		echo "m130725_232418_goal does not support migration down.\n";
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