<?php

class m130927_075445_add_column_is_diplay_popup_in_corporate extends CDbMigration
{
	public function up()
	{
        $this->addColumn("user_account_corporate", "is_display_referrals_popup", "TINYINT (1) DEFAULT 0");
	}

	public function down()
	{
        $this->dropColumn("user_account_corporate", "is_display_referrals_popup");
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