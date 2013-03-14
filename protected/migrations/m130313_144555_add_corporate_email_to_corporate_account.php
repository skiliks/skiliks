<?php

class m130313_144555_add_corporate_email_to_corporate_account extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_corporate', 'corporate_email', 'VARCHAR(120)');
	}

	public function down()
	{
        $this->dropColumn('user_account_corporate', 'corporate_email');
	}
}