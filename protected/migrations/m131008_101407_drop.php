<?php

class m131008_101407_drop extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('user', 'is_check');
	}

	public function down()
	{
		echo "m131008_101407_drop does not support migration down.\n";
		return true;
	}
}