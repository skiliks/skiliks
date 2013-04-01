<?php

class m130401_111522_add_sim_type_to_invite extends CDbMigration
{
	public function up()
	{
        $this->addColumn('invites', 'simulation_type', 'VARCHAR(20) DEFAULT '.Simulation::TYPE_FULL);
        $this->update('invites', ['simulation_type' => 1]);
	}

	public function down()
	{
        $this->dropColumn('invites', 'simulation_type');
	}
}