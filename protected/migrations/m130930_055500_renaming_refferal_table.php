<?php

class m130930_055500_renaming_refferal_table extends CDbMigration
{
	public function up()
	{
        $this->renameTable("referrals", "user_referral");
        $this->renameColumn("user_referral", "referrer_email", "referral_email");

	}

	public function down()
	{
        $this->renameTable("user_referral", "referrals");
        $this->renameColumn("referrals", "referral_email", "referrer_email");
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