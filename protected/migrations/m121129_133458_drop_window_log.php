<?php

class m121129_133458_drop_window_log extends CDbMigration
{
	public function up()
	{
        $this->dropTable('window_log');
	}

	public function down()
	{
        $this->createTable('window_log', array(
            'id'         => 'pk',
            'sim_id'     => 'INT(11) NOT NULL',
            'activeWindow'     => "INT(11) DEFAULT NULL COMMENT 'Симуляция'",
            'activeSubWindow' => "TINYINT(1) DEFAULT 0 COMMENT 'Активное подокно'",
            'timeStart' => "INT(11) DEFAULT 0 COMMENT 'Игровое время - start'",
            'timeEnd'   => "INT(11) DEFAULT 0 COMMENT 'Игровое время - end'"
        ));
	}
}