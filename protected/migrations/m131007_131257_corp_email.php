<?php

class m131007_131257_corp_email extends CDbMigration
{
	public function up()
	{
        //$this->addColumn('user_account_corporate', 'corporate_email', 'varchar(120) DEFAULT NULL');
	}

	public function down()
	{
		$this->dropColumn('user_account_corporate', 'corporate_email');
	}
}