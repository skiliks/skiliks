<?php

class m130310_153220_rename_tables extends CDbMigration
{
	public function up()
	{
        $this->renameTable('events_samples', 'event_sample');
        $this->renameTable('learning_goals', 'learning_goal');
        $this->renameTable('characters_points_titles', 'hero_behaviour');
	}

	public function down()
	{
        $this->renameTable('event_sample', 'events_samples');
        $this->renameTable('learning_goal', 'learning_goals');
        $this->renameTable('hero_behaviour', 'characters_points_titles');
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