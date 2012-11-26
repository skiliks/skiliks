<?php

class m121123_141659_log_windows extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_windows', array(
            'id'         => 'pk',
            'sim_id'     => 'INT(11) NOT NULL',
            'window'     => 'TINYINT(4) DEFAULT NULL',
            'sub_window' => 'TINYINT(4) DEFAULT NULL',
            'start_time' => 'TIME NOT NULL',
            'end_time'   => "TIME NOT NULL DEFAULT '00:00:00'"
        ));
	}

	public function down()
	{
		$this->dropTable('log_windows');
	}
}