<?php

class m130718_131600_log_meeting_end_time extends CDbMigration
{
	public function up()
	{
        $this->renameColumn('log_meeting', 'game_time', 'start_time');
        $this->addColumn('log_meeting', 'end_time', 'time');
	}

	public function down()
	{
		$this->dropColumn('log_meeting', 'end_time');
        $this->renameColumn('log_meeting', 'start_time', 'game_time');
	}
}