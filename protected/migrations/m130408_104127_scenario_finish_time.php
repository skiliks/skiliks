<?php

class m130408_104127_scenario_finish_time extends CDbMigration
{
	public function up()
	{
        $this->addColumn('scenario', 'finish_time', 'time');
	}

	public function down()
	{
		$this->dropColumn('scenario', 'finish_time');
	}
}