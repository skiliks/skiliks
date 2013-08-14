<?php

class m130731_150635_k extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulation_learning_goal_group', 'coefficient', 'decimal(10,2) default null');
	}

	public function down()
	{
        $this->dropColumn('simulation_learning_goal_group', 'coefficient');
	}

}