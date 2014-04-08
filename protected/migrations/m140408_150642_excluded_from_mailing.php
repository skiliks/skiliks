<?php

class
m140408_150642_excluded_from_mailing extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_corporate', 'excluded_from_mailing', 'tinyint(1) default 0');
	}

	public function down()
	{

	}

}