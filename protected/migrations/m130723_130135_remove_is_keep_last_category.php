<?php

class m130723_130135_remove_is_keep_last_category extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('activity_action', 'is_keep_last_category');
        $this->dropColumn('log_activity_action_agregated', 'is_keep_last_category');
        $this->dropColumn('log_activity_action_agregated_214d', 'is_keep_last_category');
	}

	public function down()
	{
		echo "m130723_130135_remove_is_keep_last_category does not support migration down.\n";
		return false;
	}

}