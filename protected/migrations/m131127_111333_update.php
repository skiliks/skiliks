<?php

class m131127_111333_update extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_corporate', 'expire_invite_rule', "varchar(15) default 'standard'");
	}

	public function down()
	{
        $this->dropColumn('user_account_corporate', 'expire_invite_rule');
	}
}