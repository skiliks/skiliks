<?php

class m130809_101307_sim_emergency_allowed extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulations', 'is_emergency_panel_allowed', 'tinyint(1) default 0');
	}

	public function down()
	{
		$this->dropColumn('simulations', 'is_emergency_panel_allowed');
	}
}