<?php

class m130730_113251_fix_decimals extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('simulation_learning_goal', 'total_positive', 'decimal(10,2)');
        $this->alterColumn('simulation_learning_goal', 'total_negative', 'decimal(10,2)');
        $this->alterColumn('simulation_learning_goal', 'max_positive', 'decimal(10,2)');
        $this->alterColumn('simulation_learning_goal', 'max_negative', 'decimal(10,2)');

        $this->alterColumn('simulation_learning_goal_group', 'total_positive', 'decimal(10,2)');
        $this->alterColumn('simulation_learning_goal_group', 'total_negative', 'decimal(10,2)');
        $this->alterColumn('simulation_learning_goal_group', 'max_positive', 'decimal(10,2)');
        $this->alterColumn('simulation_learning_goal_group', 'max_negative', 'decimal(10,2)');
    }

	public function down()
	{
        $this->alterColumn('simulation_learning_goal', 'total_positive', 'decimal(4,4)');
        $this->alterColumn('simulation_learning_goal', 'total_negative', 'decimal(4,4)');
        $this->alterColumn('simulation_learning_goal', 'max_positive', 'decimal(4,4)');
        $this->alterColumn('simulation_learning_goal', 'max_negative', 'decimal(4,4)');

        $this->alterColumn('simulation_learning_goal_group', 'total_positive', 'decimal(4,4)');
        $this->alterColumn('simulation_learning_goal_group', 'total_negative', 'decimal(4,4)');
        $this->alterColumn('simulation_learning_goal_group', 'max_positive', 'decimal(4,4)');
        $this->alterColumn('simulation_learning_goal_group', 'max_negative', 'decimal(4,4)');
	}
}