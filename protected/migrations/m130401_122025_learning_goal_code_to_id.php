<?php

class m130401_122025_learning_goal_code_to_id extends CDbMigration
{
	public function up()
	{
        $this->addColumn('hero_behaviour', 'learning_goal_id', 'int NOT NULL');
        $this->addForeignKey('hero_behaviour_learning_goal', 'hero_behaviour', 'learning_goal_id','learning_goals', 'id', 'cascade', 'cascade');
        $this->dropColumn('hero_behaviour', 'learning_goal_code');
	}

	public function down()
	{
		echo "m130401_122025_learning_goal_code_to_id does not support migration down.\n";
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