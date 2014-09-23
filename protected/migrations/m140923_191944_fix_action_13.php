<?php

class m140923_191944_fix_action_13 extends CDbMigration
{
	public function up()
	{
        $this->delete('permission', ' action = 13 ');
	}

	public function down()
	{
		echo "m140923_191944_fix_action_13 does not support migration down.\n";
	}
}