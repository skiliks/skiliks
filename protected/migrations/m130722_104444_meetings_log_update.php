<?php

class m130722_104444_meetings_log_update extends CDbMigration
{
	public function up()
	{
        $this->addColumn('log_activity_action', 'meeting_id', 'int default null');
        $this->addColumn('log_meeting', 'window_uid', 'varchar(32) default null');
	}

	public function down()
	{
		$this->dropColumn('log_activity_action', 'meeting_id');
		$this->dropColumn('log_meeting', 'window_uid');
	}
}