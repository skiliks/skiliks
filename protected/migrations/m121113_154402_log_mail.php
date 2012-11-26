<?php

class m121113_154402_log_mail extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_mail', array(
            'id'         => 'pk',
            'sim_id'     => 'INT(11) NOT NULL',
            'mail_id'    => 'INT(11) DEFAULT NULL',
            'window'     => 'TINYINT(4) DEFAULT NULL',
            'start_time' => 'TIME NOT NULL',
            'end_time'   => "TIME NOT NULL DEFAULT '00:00:00'"
        ));
	}

	public function down()
	{
        $this->dropTable('log_mail');
	}

}