<?php

class m140314_105656_cache_full_report_simulations extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_corporate', 'cache_full_report', 'blob default null');
	}

	public function down()
	{
		$this->dropColumn('user_account_corporate', 'cache_full_report');
	}

}