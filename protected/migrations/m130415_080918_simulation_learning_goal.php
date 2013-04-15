<?php

class m130415_080918_simulation_learning_goal extends CDbMigration
{
	public function up()
	{
        $this->createTable('simulation_learning_goal', [
            'id' => 'pk',
            'sim_id' => 'int not null',
            'learning_goal_id' => 'int not null',
            'value' => 'decimal(10, 2)',
            'percent' => 'decimal(10, 2)',
            'problem' => 'decimal(10, 2)'
        ]);

        $this->addForeignKey(
            'fk_simulation_learning_goal_sim_id',
            'simulation_learning_goal', 'sim_id',
            'simulations', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_simulation_learning_goal_learning_goal_id',
            'simulation_learning_goal', 'learning_goal_id',
            'learning_goal', 'id',
            'CASCADE', 'CASCADE'
        );
	}

	public function down()
	{
		$this->dropForeignKey('fk_simulation_learning_goal_sim_id', 'simulation_learning_goal');
		$this->dropForeignKey('fk_simulation_learning_goal_learning_goal_id', 'simulation_learning_goal');

        $this->dropTable('simulation_learning_goal');
	}
}