<?php

class m130731_142715_fix_body_in_log_server_request extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('log_server_request', 'response_body', 'LONGBLOB');
        $this->alterColumn('profile', 'lastname', 'TEXT');
        $this->alterColumn('profile', 'firstname', 'TEXT');
        $this->alterColumn('user', 'username', 'VARCHAR(200)');
	}

	public function down()
	{
		echo "m130731_142715_fix_body_in_log_server_request does not support migration down.\n";
	}
}