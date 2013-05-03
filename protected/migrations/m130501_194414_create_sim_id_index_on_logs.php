<?php

class m130501_194414_create_sim_id_index_on_logs extends CDbMigration
{
	public function up()
	{
        $this->createIndex('log_windows_sim_id', 'log_windows', 'sim_id');
        $this->createIndex('log_dialogs_sim_id', 'log_dialogs', 'sim_id');
        $this->createIndex('log_mail_sim_id', 'log_mail', 'sim_id');
        $this->createIndex('log_documents_sim_id', 'log_documents', 'sim_id');
	}

	public function down()
	{
		echo "m130501_194414_create_sim_id_index_on_logs does not support migration down.\n";
		return false;
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