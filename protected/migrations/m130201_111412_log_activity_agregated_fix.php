<?php

class m130201_111412_log_activity_agregated_fix extends CDbMigration
{
	public function up()
	{
        $this->addColumn('log_activity_action_agregated', 'is_keep_last_category', 'TINYINT(1)');
        $this->dropColumn('log_activity_action_agregated', 'keep_last_category');
	}

	public function down()
	{
        $this->addColumn('log_activity_action_agregated', 'keep_last_category', 'TINYINT(1)');
        $this->dropColumn('log_activity_action_agregated', 'is_keep_last_category');
	}
}