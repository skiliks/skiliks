<?php

class m121224_064203_log_activity_fields extends CDbMigration
{
	public function up()
	{
        $this->addColumn('log_activity_action', 'mail_id', 'integer');
        $this->addColumn('log_activity_action', 'document_id', 'integer');
        $this->addForeignKey('log_activity_action_mail_id','log_activity_action', 'mail_id', 'mail_box', 'id', 'cascade', 'cascade');
        $this->addForeignKey('log_activity_action_document_id','log_activity_action', 'document_id', 'my_documents', 'id', 'cascade', 'cascade');
	}

	public function down()
	{
        yecho "m121224_064203_log_activity_fields does not support migration down.\n";
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