<?php

class m130201_090015_create_log_activity_agregated extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_activity_action_agregated', [
            'id'                 => 'pk',
            'sim_id'             => 'INT NOT NULL',
            'leg_type'           => 'VARCHAR(30) COMMENT \'Just text label\'',
            'leg_action'         => 'VARCHAR(30) COMMENT \'Just text label\'',
            'activity_action_id' => 'INT',
            'category'           => 'VARCHAR(30) COMMENT \'Just text label\'',
            'keep_last_category' => 'TINYINT(1)',
            'start_time'         => 'TIME NOT NULL',
            'end_time'           => 'TIME NOT NULL',
            'duration'           => 'TIME NOT NULL'
        ]);
        
        $this->addForeignKey(
            'log_activity_action_agregated_FK_activity_action', 
            'log_activity_action_agregated', 'activity_action_id', 
            'activity_action', 'id'
        );
        
        $this->addForeignKey(
            'log_activity_action_agregated_FK_simulations', 
            'log_activity_action_agregated', 'sim_id', 
            'simulations', 'id'
        );
	}

	public function down()
	{
		
	}
}