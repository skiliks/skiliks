<?php

class m130407_153919_simulation_learning_area extends CDbMigration
{
	public function up()
	{
        $this->createTable("simulation_learning_area", [
            'id' => 'pk',
            'learning_area_id' => 'INT(11) NOT NULL',
            'value' => 'FLOAT(10,2) NULL'
        ]);
        $this->addForeignKey("simulation_learning_area_learning_area_id", "simulation_learning_area", "learning_area_id", "learning_area", "id");
	}

	public function down()
	{
        $this->dropForeignKey("simulation_learning_area_learning_area_id", "simulation_learning_area");
		$this->dropTable("simulation_learning_area");
	}

}