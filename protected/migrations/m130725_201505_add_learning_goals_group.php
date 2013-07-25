<?php

class m130725_201505_add_learning_goals_group extends CDbMigration
{
	public function up()
	{
        $this->createTable('learning_goal_group', [
            'id'=>'pk',
            'code'=>'varchar(5) not null',
            'title'=>'text default null',
            'import_id' => 'varchar(14) NOT NULL DEFAULT \'00000000000000\'',
            'scenario_id' => 'int(11) NOT NULL'
        ]);
        $this->createTable('simulation_learning_goal_group', [
            'id'=>'pk',
            'sim_id' => 'int(11) NOT NULL',
            'learning_goal_group_id' => 'int(11) not null',
            'value' => 'decimal(10,2) DEFAULT NULL',
            'percent' => 'decimal(10,2) DEFAULT NULL',
            'problem' => 'decimal(10,2) DEFAULT NULL'
        ]);
        $this->addForeignKey('fk_simulation_learning_goal_group_sim_id', 'simulation_learning_goal_group', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_simulation_learning_goal_group_learning_goal_group_id', 'simulation_learning_goal_group', 'learning_goal_group_id', 'learning_goal_group', 'id', 'CASCADE', 'CASCADE');

    }

	public function down()
	{
		$this->dropForeignKey('fk_simulation_learning_goal_group_sim_id', 'simulation_learning_goal_group');
        $this->dropForeignKey('fk_simulation_learning_goal_group_learning_goal_group_id', 'simulation_learning_goal_group');
        $this->dropTable('simulation_learning_goal_group');
        $this->dropTable('learning_goal_group');
    }

}