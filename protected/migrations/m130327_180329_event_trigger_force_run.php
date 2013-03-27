<?php

class m130327_180329_event_trigger_force_run extends CDbMigration
{
	public function up()
	{
        $this->addColumn('events_triggers', 'force_run', 'TINYINT(1) NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('events_triggers', 'force_run');
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