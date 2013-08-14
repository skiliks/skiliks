<?php

class m130814_162922_update_log_server_request extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('log_server_request', 'request_body', 'LONGBLOB');
	}

	public function down()
	{
		echo "m130814_162922_update_log_server_request does not support migration down.\n";
	}
}