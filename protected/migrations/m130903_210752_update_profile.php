<?php

class m130903_210752_update_profile extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('profile', 'timestamp', 'INT(20)');
        $this->alterColumn('user', 'createtime', 'INT(20)');
        $this->alterColumn('user', 'lastvisit', 'INT(20)');
        $this->alterColumn('user', 'lastaction', 'INT(20)');
        $this->alterColumn('user', 'lastpasswordchange', 'INT(20)');
        $this->alterColumn('user', 'failedloginattempts', 'INT(20)');
	}

	public function down()
	{
		echo "m130903_210752_update_profile does not support migration down.\n";
	}
}