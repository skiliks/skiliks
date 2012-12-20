<?php

class m121219_205304_dialogs extends CDbMigration
{
	public function up()
	{
            $this->createTable('log_dialogs', array(
                'id'         => 'pk',
                'sim_id'     => 'INT(11) NOT NULL',
                'dialog_id'  => 'INT(11) DEFAULT NULL',
                'last_id'     => 'INT(11) DEFAULT NULL',
                'start_time' => 'TIME NOT NULL',
                'end_time'   => "TIME NOT NULL DEFAULT '00:00:00'"
            ));
	}

	public function down()
	{
            $this->dropTable('log_dialogs');
	}
}