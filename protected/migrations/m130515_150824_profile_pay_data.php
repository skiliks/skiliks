<?php

class m130515_150824_profile_pay_data extends CDbMigration
{
	public function up()
	{
        $this->addColumn("user_account_corporate", "inn", "varchar(50) DEFAULT NULL");
        $this->addColumn("user_account_corporate", "cpp", "varchar(50) DEFAULT NULL");
        $this->addColumn("user_account_corporate", "bank_account_number", "varchar(50) DEFAULT NULL");
        $this->addColumn("user_account_corporate", "bic", "varchar(50) DEFAULT NULL");
        $this->addColumn("user_account_corporate", "preference_payment_method", "varchar(50) DEFAULT NULL");
	}

	public function down()
	{
        $this->dropColumn("user_account_corporate", "inn");
        $this->dropColumn("user_account_corporate", "cpp");
        $this->dropColumn("user_account_corporate", "bank_account_number");
        $this->dropColumn("user_account_corporate", "bic");
        $this->dropColumn("user_account_corporate", "preference_payment_method");
	}

}