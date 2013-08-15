<?php

class m130815_153151_clear extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('universal_log_activity_action_id', 'universal_log');
        $this->dropColumn('universal_log', 'activity_action_id');
	}

	public function down()
	{
		echo "m130815_153151_clear does not support migration down.\n";
		return false;
	}

}