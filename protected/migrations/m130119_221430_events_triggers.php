<?php

class m130119_221430_events_triggers extends CDbMigration
{
	public function up()
	{
        $this->delete('events_triggers');
        $this->alterColumn('events_triggers', 'trigger_time', 'time DEFAULT NULL');
	}

	public function down()
	{
        $this->alterColumn('events_triggers', 'trigger_time', 'int(11) NOT NULL');
	}

}