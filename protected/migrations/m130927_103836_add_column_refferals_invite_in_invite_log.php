<?php

class m130927_103836_add_column_refferals_invite_in_invite_log extends CDbMigration
{
    public function up()
    {
        $this->addColumn("log_account_invite", "invites_limit_referrals", "INT (11) DEFAULT NULL");
    }

    public function down()
    {
        $this->dropColumn("log_account_invite", "invites_limit_referrals");
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