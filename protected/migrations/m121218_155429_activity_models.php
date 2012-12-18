<?php

class m121218_155429_activity_models extends CDbMigration
{
	public function up()
	{
        $this->createTable('activity', array(
            'id' => 'VARCHAR(10) NOT NULL PRIMARY KEY',
            'parent' => 'VARCHAR(10) NOT NULL',
            'grandparent' => 'VARCHAR(10) NOT NULL',
            'name' => 'string NOT NULL',
            'category_id' => 'int NOT NULL'
        ));
        $this->createTable('activity_action', array(
            'id' => 'pk',
            'activity_id' => 'varchar(10) NOT NULL',
            'dialog_id' => 'integer',
            'mail_id' => 'integer',
            'document_id' => 'integer'
        ));
        $this->addForeignKey('fk_activity_action_action_id', 'activity_action', 'activity_id', 'activity', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_activity_action_dialog_id', 'activity_action', 'dialog_id', 'dialogs', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_activity_action_mail_id', 'activity_action', 'mail_id', 'mail_template', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_activity_action_document_id', 'activity_action', 'document_id', 'my_documents_template', 'id', 'CASCADE', 'CASCADE');
    }

	public function down()
	{
		$this->dropTable('activity_action');
        $this->dropTable('activity');
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