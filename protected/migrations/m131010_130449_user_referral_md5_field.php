<?php

class m131010_130449_user_referral_md5_field extends CDbMigration
{
    public function up()
    {
        $this->addColumn("user_referral", "hash", "varchar (200) DEFAULT NULL");
    }

    public function down()
    {
        $this->dropColumn("user_referral","hash");
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