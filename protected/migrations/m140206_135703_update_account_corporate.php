<?php

class m140206_135703_update_account_corporate extends CDbMigration
{
	public function up()
	{
        $this->addColumn(UserAccountCorporate::model()->tableName(), 'site', 'varchar(250) default null');
        $this->addColumn(UserAccountCorporate::model()->tableName(), 'description_for_sales', 'text default null');
        $this->addColumn(UserAccountCorporate::model()->tableName(), 'contacts_for_sales', 'text default null');
        $this->addColumn(UserAccountCorporate::model()->tableName(), 'status_for_sales', 'varchar(250) default null');
	}

	public function down()
	{
        $this->dropColumn(UserAccountCorporate::model()->tableName(), 'site');
        $this->dropColumn(UserAccountCorporate::model()->tableName(), 'description_for_sales');
        $this->dropColumn(UserAccountCorporate::model()->tableName(), 'contacts_for_sales');
        $this->dropColumn(UserAccountCorporate::model()->tableName(), 'status_for_sales');
	}

}