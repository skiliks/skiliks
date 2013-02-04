<?php

class m130204_092855_fix_FK_in_log_activity_action_agregated extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('log_activity_action_agregated_FK_activity_action', 'log_activity_action_agregated');
		$this->dropForeignKey('log_activity_action_agregated_FK_simulations', 'log_activity_action_agregated');
        
        $this->addForeignKey(
            'log_activity_action_agregated_FK_activity_action', 
            'log_activity_action_agregated', 'activity_action_id', 
            'activity_action', 'id',
            'CASCADE', 'CASCADE'
        );
        
        $this->addForeignKey(
            'log_activity_action_agregated_FK_simulations', 
            'log_activity_action_agregated', 'sim_id', 
            'simulations', 'id',
            'CASCADE', 'CASCADE'
        );                
	}

	public function down()
	{
		echo "m130204_092855_fix_FK_in_log_activity_action_agregated hasn`t down migration.\n";
	}
}