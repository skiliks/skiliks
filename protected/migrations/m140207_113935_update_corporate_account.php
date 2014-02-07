<?php

class m140207_113935_update_corporate_account extends CDbMigration
{
	public function up()
	{
        $this->addColumn(UserAccountCorporate::model()->tableName(), 'company_name_for_sales', 'varchar(255) default null');
        $this->addColumn(UserAccountCorporate::model()->tableName(), 'industry_for_sales', 'varchar(255) default null');
	}

	public function down()
	{
        $this->dropColumn(UserAccountCorporate::model()->tableName(), 'company_name_for_sales');
        $this->dropColumn(UserAccountCorporate::model()->tableName(), 'industry_for_sales');
	}

}