<?php

class m121220_000103_log_dialog extends CDbMigration
{
	public function up()
	{
            $this->createTable('log_dialog_points', array(
                'id'         => 'pk',
                'sim_id'     => 'INT(11) NOT NULL',
                'point_id'  => 'INT(11) DEFAULT NULL',
                'dialog_id'  => 'INT(11) DEFAULT NULL',
            ));
            
            $this->dropTable('log_dialog');
	}

	public function down()
	{
            $this->dropTable('log_dialog_points');
            
            $this->createTable('log_dialog', array(
                'id'         => 'pk',
                'sim_id'     => 'INT(11) NOT NULL',
                'point_id'  => 'INT(11) DEFAULT NULL',
                'dialog_id'  => 'INT(11) DEFAULT NULL',
            ));
	}
}