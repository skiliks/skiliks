<?php

class m131011_120834_remaking_field_to_message_property extends CDbMigration
{
	public function up()
	{
        $this->alterColumn("log_account_invite", "message", "TEXT");
	}

	public function down()
	{
        $this->alterColumn("log_account_invite", "message", "varchar (100)");
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