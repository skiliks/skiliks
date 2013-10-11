<?php

class m131010_132340_user_referral_md5_field_change_name extends CDbMigration
{
	public function up()
	{
        $this->renameColumn("user_referral", "hash", "uniqueid");
	}

	public function down()
	{
		echo "m131010_132340_user_referral_md5_field_change_name does not support migration down.\n";
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