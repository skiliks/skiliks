<?php

class m140220_123917_drop_tariff_referrals extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('fk_invites_tariff_plan_id','invites');
        $this->dropColumn('invites', 'tariff_plan_id');
        $this->dropColumn('invites', 'expired_at');
        $this->dropForeignKey('tariff_id_key','invoice');
        $this->dropColumn('invoice', 'tariff_id');
        $this->dropForeignKey('fk_tariff_plan_invoice_id','tariff_plan');
        $this->dropForeignKey('fk_tariff_plan_tariff_id','tariff_plan');
        $this->dropForeignKey('fk_tariff_plan_user_id','tariff_plan');
        $this->dropForeignKey('user_account_corporate_fk_tariff','user_account_corporate');
        $this->dropColumn('user_account_corporate', 'tariff_id');
        $this->dropColumn('user_account_corporate', 'tariff_activated_at');
        $this->dropColumn('user_account_corporate', 'tariff_expired_at');
        $this->dropColumn('user_account_corporate', 'referrals_invite_limit');
        $this->dropColumn('user_account_corporate', 'is_display_referrals_popup');
        $this->dropColumn('user_account_corporate', 'is_display_tariff_expire_pop_up');
        $this->dropColumn('user_account_corporate', 'expire_invite_rule');
        $this->dropForeignKey('referral_id_fk','user_referral');
        $this->dropColumn('log_account_invite', 'invites_limit_referrals');

        $this->dropTable('user_referral');
        $this->dropTable('tariff');
        $this->dropTable('tariff_plan');
	}

	public function down()
	{
		echo "m140220_123917_drop_tariff_referrals does not support migration down.\n";
		return false;
	}
}