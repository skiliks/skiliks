<?php

class m130718_085421_flag_time_switch_to_queue extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulation_flag_queue', 'value', 'tinyint(1) default null');
	}

	public function down()
	{
		$this->dropColumn('simulation_flag_queue', 'value');
	}
}