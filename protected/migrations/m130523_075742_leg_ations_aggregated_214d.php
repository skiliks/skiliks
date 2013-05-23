<?php

class m130523_075742_leg_ations_aggregated_214d extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_activity_action_agregated_214d',[
            'id'=>'pk',
            'sim_id' => 'int(11) NOT NULL',
            'leg_type' => "varchar(30) DEFAULT NULL COMMENT 'Just text label'",
            'leg_action' => "varchar(30) DEFAULT NULL COMMENT 'Just text label'",
            'activity_action_id' => "int(11) DEFAULT NULL",
            'category' => "varchar(30) DEFAULT NULL COMMENT 'Just text label'",
            'start_time' => "time NOT NULL",
            'end_time' => "time NOT NULL",
            'duration' => "time NOT NULL",
            'is_keep_last_category' => "tinyint(1) DEFAULT NULL"
        ]);
        $this->addForeignKey('log_activity_action_agregated_214d_FK_activity_action', 'log_activity_action_agregated_214d', 'activity_action_id', 'activity_action', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('log_activity_action_agregated_214d_FK_simulations', 'log_activity_action_agregated_214d', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('log_activity_action_agregated_214d_FK_activity_action', 'log_activity_action_agregated_214d');
        $this->dropForeignKey('log_activity_action_agregated_214d_FK_simulations', 'log_activity_action_agregated_214d');
        $this->dropTable('log_activity_action_agregated_214d');
    }
}