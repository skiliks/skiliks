<?php

class m131002_081021_clear extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('user_account_corporate', 'corporate_email'); //is_corporate_email_verified
        $this->dropColumn('user_account_corporate', 'is_corporate_email_verified');//corporate_email_verified_at
        $this->dropColumn('user_account_corporate', 'corporate_email_verified_at'); //corporate_email_activation_code
        $this->dropColumn('user_account_corporate', 'corporate_email_activation_code');
	}

	public function down()
	{
		echo "Done \n";
		return true;
	}

}