<?php

class m131009_121149_update_field_type_in_referrals extends CDbMigration
{
	public function up()
	{
        $this->alterColumn("user_referral", "reject_reason", "TEXT");
	}

	public function down()
	{
		echo "m131009_121149_update_field_type_in_referrals does not support migration down.\n";
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