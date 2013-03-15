<?php

class m130315_100119_add_activation_code_for_corporate_user_account extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_corporate', 'corporate_email_activation_code', 'VARCHAR(128) DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn('user_account_corporate', 'corporate_email_activation_code');
	}
}