<?php

class m130815_153151_clear extends CDbMigration
{
	public function up()
	{
        try {
            $this->dropForeignKey('universal_log_activity_action_id', 'universal_log');
        } catch (CDbException $e) {
            // just to run migration
        }

        $this->dropColumn('universal_log', 'activity_action_id');
	}

	public function down()
	{
		echo "m130815_153151_clear does not support migration down.\n";
		return false;
	}

}