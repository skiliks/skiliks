<?php

class m140124_154414_update_tariff_expired_type extends CDbMigration
{
	public function up()
	{
        // $this->alterColumn('user_account_corporate', 'expire_invite_rule', "varchar(15) default '".UserAccountCorporate::EXPIRE_INVITE_RULE_BY_TARIFF."'");

        /* @var UserAccountCorporate[] $accounts  */
//        $accounts = UserAccountCorporate::model()->findAll();
//        foreach( $accounts as $account ) {
//            $account->expire_invite_rule = UserAccountCorporate::EXPIRE_INVITE_RULE_BY_TARIFF;
//            $account->save(false);
//        }
	}

	public function down()
	{
		echo "m140124_154414_update_tariff_expired_type does not support migration down.\n";
		return true;
	}

}