<?php

class m130801_141220_score extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulation_learning_area', 'score', 'decimal(10,2) default null');
	}

	public function down()
	{
        $this->dropColumn('simulation_learning_area', 'score');
	}

}