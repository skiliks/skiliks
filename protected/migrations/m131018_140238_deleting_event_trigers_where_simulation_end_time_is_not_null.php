<?php

class m131018_140238_deleting_event_trigers_where_simulation_end_time_is_not_null extends CDbMigration
{
	public function up()
	{
        $this->execute("DELETE events_triggers.*
                        FROM events_triggers
                        JOIN simulations ON simulations.id = events_triggers.sim_id
                        WHERE simulations.end IS NOT NULL
        ");
	}

	public function down()
	{
        echo "Migrate down is not supporting";
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