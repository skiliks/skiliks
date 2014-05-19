<?php

class m140303_093427_discount extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_corporate', 'discount', 'float(6,2) default 0');
        $this->addColumn('user_account_corporate', 'start_discount', 'date default null');
        $this->addColumn('user_account_corporate', 'end_discount', 'date default null');
	}

	public function down()
	{
		$this->dropColumn('user_account_corporate', 'discount');
		$this->dropColumn('user_account_corporate', 'start_discount');
		$this->dropColumn('user_account_corporate', 'end_discount');
	}

}