<?php

class m130314_163837_add_is_active_for_corporate_account extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_corporate', 'is_corporate_email_verified', 'TINYINT(1) DEFAULT 0');
        $this->addColumn('user_account_corporate', 'corporate_email_verified_at', 'DATETIME DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn('user_account_corporate', 'is_corporate_email_verified');
		$this->dropColumn('user_account_corporate', 'corporate_email_verified_at');
	}
}