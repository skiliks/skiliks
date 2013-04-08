<?php

class m130408_094035_add_sim_id extends CDbMigration
{
	public function up()
	{
        $this->addColumn("simulation_learning_area", 'sim_id', "INT(11) NOT NULL");
        $this->addForeignKey("simulation_learning_area_sim_id", "simulation_learning_area", "sim_id", "simulations", "id", 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey("simulation_learning_area_sim_id", "simulation_learning_area");
        $this->dropColumn("simulation_learning_area", 'sim_id');
	}

}