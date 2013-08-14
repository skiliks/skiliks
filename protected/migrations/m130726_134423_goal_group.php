<?php

class m130726_134423_goal_group extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulation_learning_goal_group', 'total_positive', 'decimal(4,4) default 0');
        $this->addColumn('simulation_learning_goal_group', 'total_negative', 'decimal(4,4) default 0');
        $this->addColumn('simulation_learning_goal_group', 'max_positive',   'decimal(4,4) default 0');
        $this->addColumn('simulation_learning_goal_group', 'max_negative',   'decimal(4,4) default 0');
	}

	public function down()
	{
		echo "m130726_134423_goal_group does not support migration down.\n";
		return false;
	}
}