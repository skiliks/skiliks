<?php

class m130330_000405_unique_keys_p1 extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('fk_learning_goal_learning_area_code', 'learning_goal');
        $this->dropColumn('learning_goal', 'learning_area_code');
        $this->addColumn('learning_goal', 'learning_area_code', 'int NOT NULL');
        $this->dropIndex('PRIMARY', 'learning_area');
        $this->addColumn('learning_area', 'id', 'pk');
        $this->addForeignKey('learning_goal_area', 'learning_goal', 'learning_area_code', 'learning_area', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('learning_area_uniq', 'learning_area', 'code, scenario_id', true);
	}

	public function down()
	{
		echo "m130330_000405_unique_keys_p1 does not support migration down.\n";
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