<?php

class m131002_082033_adding_reject_reason_to_refferal extends CDbMigration
{
	public function up()
	{
        $this->addColumn("user_referral", "reject_reason", "VARCHAR (200) DEFAULT NULL");
        $this->addColumn("user_referral", "status", "VARCHAR (50) DEFAULT NULL");
	}

	public function down()
	{
        $this->dropColumn("user_referral", "reject_reason");
        $this->dropColumn("user_referral", "status");
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