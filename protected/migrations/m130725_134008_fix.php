<?php

class m130725_134008_fix extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulation_learning_goal', 'total_positive', 'decimal(4,4) default 0');
        $this->addColumn('simulation_learning_goal', 'total_negative', 'decimal(4,4) default 0');
        $this->addColumn('simulation_learning_goal', 'max_positive',   'decimal(4,4) default 0');
        $this->addColumn('simulation_learning_goal', 'max_negative',   'decimal(4,4) default 0');
	}

	public function down()
	{
		echo "m130725_134008_fix does not support migration down.\n";
		return false;
	}

}