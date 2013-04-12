<?php

class m130412_144732_add_window_uid_to_all_logs extends CDbMigration
{
	public function up()
	{
        $this->addColumn('log_documents', 'window_uid', 'VARCHAR(100) NOT NULL');
        $this->addColumn('log_dialogs', 'window_uid', 'VARCHAR(100) NOT NULL');
	}

	public function down()
	{
        $this->dropColumn('log_documents', 'window_uid');
        $this->dropColumn('log_dialogs', 'window_uid');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}