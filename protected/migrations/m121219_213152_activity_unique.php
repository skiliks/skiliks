<?php

class m121219_213152_activity_unique extends CDbMigration
{
	public function up()
	{
        $this->createIndex('activity_action_activity_id','activity_action','activity_id');
        $this->createIndex('activity_action_document_unique','activity_action','activity_id, document_id', true);
        $this->createIndex('activity_action_dialog_unique','activity_action','activity_id, dialog_id', true);
        $this->createIndex('activity_action_mail_unique','activity_action','activity_id, mail_id', true);
	}

	public function down()
	{
        $this->dropIndex('activity_action_activity_id','activity_action');
        $this->dropIndex('activity_action_activity_id','activity_action');
        $this->dropIndex('activity_action_document_unique','activity_action');
        $this->dropIndex('activity_action_dialog_unique','activity_action');
        $this->dropIndex('activity_action_mail_unique','activity_action');

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