<?php

class m140410_094608_site_log_account_action_update extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('site_log_account_action', 'message', 'blob default null');
	}

	public function down()
	{
	}
}