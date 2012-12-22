<?php

class m121221_213647_add_cascade_to_log_activity extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('activity_action_id','log_activity_action');
        $this->dropForeignKey('log_activity_action_sim_id','log_activity_action');
        $this->addForeignKey('activity_action_id', 'log_activity_action', 'activity_action_id', 'activity_action', 'id','CASCADE', 'CASCADE');
        $this->addForeignKey('log_activity_action_sim_id', 'log_activity_action', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		echo "m121221_213647_add_cascade_to_log_activity does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}