<?php

class m130307_164406_remove_unused_dialog_tables extends CDbMigration
{
	public function up()
	{
        $this->dropTable('simulations_dialogs_durations');
        $this->dropTable('simulations_dialogs_points');
	}

	public function down()
	{
        $this->createTable('simulations_dialogs_durations', [
            'id' => 'pk',
            'sim_id' => 'INT NOT NULL',
            'duration' => 'INT NOT NULL'
        ]);

        $this->addForeignKey('fk_simulations_dialogs_durations_sim_id', 'simulations_dialogs_durations', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('simulations_dialogs_points', [
            'id' => 'pk',
            'sim_id' => 'INT NOT NULL',
            'point_id' => 'INT NOT NULL',
            'count' => 'INT NOT NULL',
            'value' => 'INT NOT NULL',
            'count6x' => 'INT NOT NULL',
            'value6x' => 'INT NOT NULL',
            'count_negative' => 'INT NOT NULL',
            'value_negative' => 'INT NOT NULL',
        ]);

        $this->addForeignKey('fk_simulations_dialogs_points_sim_id', 'simulations_dialogs_points', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_simulations_dialogs_points_point_id', 'simulations_dialogs_points', 'point_id', 'characters_points_titles', 'id', 'CASCADE', 'CASCADE');
	}
}