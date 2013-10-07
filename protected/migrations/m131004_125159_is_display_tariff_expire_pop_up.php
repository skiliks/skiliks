<?php

class m131004_125159_is_display_tariff_expire_pop_up extends CDbMigration
{
	public function up()
	{
        $this->addColumn("user_account_corporate", "is_display_tariff_expire_pop_up", "TINYINT (1) DEFAULT 0");
	}

	public function down()
	{
        $this->dropColumn("user_account_corporate", "is_display_tariff_expire_pop_up");
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