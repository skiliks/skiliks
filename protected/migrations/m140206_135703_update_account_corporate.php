<?php

class m140206_135703_update_account_corporate extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_corporate', 'site', 'varchar(250) default null');
        $this->addColumn('user_account_corporate', 'description_for_sales', 'text default null');
        $this->addColumn('user_account_corporate', 'contacts_for_sales', 'text default null');
        $this->addColumn('user_account_corporate', 'status_for_sales', 'varchar(250) default null');
	}

	public function down()
	{
        $this->dropColumn('user_account_corporate', 'site');
        $this->dropColumn('user_account_corporate', 'description_for_sales');
        $this->dropColumn('user_account_corporate', 'contacts_for_sales');
        $this->dropColumn('user_account_corporate', 'status_for_sales');
	}

}