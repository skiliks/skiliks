<?php

class m131023_145859_remaking extends CDbMigration
{
	public function up()
	{
        $this->execute("UPDATE invites SET sent_time = NULL");
        $this->alterColumn("invites", "sent_time", "DATETIME");
	}

	public function down()
	{
		echo "m131023_145859_remaking does not support migration down.\n";
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