<?php

class m130708_071816_fix_log_server_request extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('log_server_request', 'backend_game_time', 'time default null');
	}

	public function down()
	{
        $this->alterColumn('log_server_request', 'backend_game_time', 'time not null');
	}
}