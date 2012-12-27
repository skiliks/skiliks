<?php

class m121227_154011_fix_events_trigger_time extends CDbMigration
{
	public function up()
    {
        $this->dbConnection->createCommand('UPDATE events_samples SET trigger_time = 0 where code = \'E13\';')->execute();

	}

	public function down()
	{

	}
}