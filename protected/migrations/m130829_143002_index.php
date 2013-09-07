<?php

class m130829_143002_index extends CDbMigration
{
	public function up()
	{
        $this->dropIndex('activity_action_dialog_unique', 'activity_action');
	}

	public function down()
	{
		echo "m130829_143002_index does not support migration down.\n";
		return false;
	}
}