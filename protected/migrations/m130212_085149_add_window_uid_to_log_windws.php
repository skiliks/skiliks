<?php

class m130212_085149_add_window_uid_to_log_windws extends CDbMigration
{
	public function up()
	{
        $this->addColumn('log_windows', 'window_uid', "VARCHAR(32) DEFAULT NULL COMMENT 'md5'");
	}

	public function down()
	{
		$this->dropColumn('log_windows', 'window_uid');
	}


}