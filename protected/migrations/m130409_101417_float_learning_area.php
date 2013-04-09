<?php

class m130409_101417_float_learning_area extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('simulation_learning_area', 'value', 'FLOAT(10,6) NULL');
	}

	public function down()
	{
        $this->alterColumn('simulation_learning_area', 'value', 'FLOAT(10,2) NULL');
	}

}