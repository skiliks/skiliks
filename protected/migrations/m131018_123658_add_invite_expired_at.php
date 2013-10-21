<?php

class m131018_123658_add_invite_expired_at extends CDbMigration
{
	public function up()
	{
        $this->addColumn("invites", "expired_at", "DATETIME");
	}

	public function down()
	{
        $this->dropColumn("invites", "expired_at");
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