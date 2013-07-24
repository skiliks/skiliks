<?php

class m130724_134200_end_time extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulation_completed_parent', 'end_time', 'time default null');
	}

	public function down()
	{
		echo "m130724_134200_end_time does not support migration down.\n";
		return false;
	}

}