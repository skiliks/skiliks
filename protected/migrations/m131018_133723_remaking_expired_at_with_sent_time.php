<?php

class m131018_133723_remaking_expired_at_with_sent_time extends CDbMigration
{
	public function up()
	{
        $this->execute('UPDATE invites SET expired_at = FROM_UNIXTIME(sent_time+432000)');
	}

	public function down()
	{
        $this->execute('UPDATE invites SET expired_at = NULL');
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