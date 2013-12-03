<?php

class m131023_150524_remaking_sent_at_to_date extends CDbMigration
{
	public function up()
	{
        $this->execute("UPDATE invites SET sent_time = DATE_SUB(expired_at, INTERVAL 5 DAY)");
	}

	public function down()
	{
		echo "m131023_150524_remaking_sent_at_to_date does not support migration down.\n";
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