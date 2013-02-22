<?php

class m130223_004121_parent_code_unique extends CDbMigration
{
	public function up()
	{
        $this->delete('simulation_completed_parent');
        $this->alterColumn('simulation_completed_parent', 'parent_code', "VARCHAR(10) NOT NULL");
        $this->createIndex('completed_parent', 'simulation_completed_parent', 'parent_code, sim_id', true);
        //$this->createIndex('completed_parent_sim_id', 'simulation_completed_parent', 'sim_id', true);
	}

	public function down()
	{
        $this->alterColumn('simulation_completed_parent', 'parent_code', "VARCHAR(5) NOT NULL");
        $this->dropIndex('completed_parent', 'simulation_completed_parent');
        //$this->dropIndex('completed_parent_sim_id', 'simulation_completed_parent');
	}

}