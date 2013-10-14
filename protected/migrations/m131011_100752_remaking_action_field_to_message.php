<?php

class m131011_100752_remaking_action_field_to_message extends CDbMigration
{
	public function up()
	{
        $this->renameColumn("log_account_invite", "action", "message");
	}

	public function down()
	{
        $this->renameColumn("log_account_invite", "message", "action");
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