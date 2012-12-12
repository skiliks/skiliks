<?php

/**
 *
 * @author slavka
 */
class m121212_164300_update_log_mail extends CDbMigration
{
    public function up()
	{
        $this->addColumn(
            'log_mail', 
            'mail_task_id', 
            "INT DEFAULT NULL COMMENT 'Id of planned taks. Null - no tasks planned.'");
	}

	public function down()
	{
        $this->dropColumn('log_mail', 'mail_task_id');
	}
}

