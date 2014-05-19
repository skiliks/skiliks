<?php

class m140207_113935_update_corporate_account extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_corporate', 'company_name_for_sales', 'varchar(255) default null');
        $this->addColumn('user_account_corporate', 'industry_for_sales', 'varchar(255) default null');

	}

	public function down()
	{
        $this->dropColumn('user_account_corporate', 'company_name_for_sales');
        $this->dropColumn('user_account_corporate', 'industry_for_sales');
	}

}