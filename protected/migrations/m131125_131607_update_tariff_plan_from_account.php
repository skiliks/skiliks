<?php

class m131125_131607_update_tariff_plan_from_account extends CDbMigration
{
	public function up()
	{

        $accounts = UserAccountCorporate::model()->findAll();
        foreach($accounts as $account) {
            $tariff_plan = TariffPlan::model()->findByAttributes(['user_id'=>$account->user_id, 'status'=>TariffPlan::STATUS_ACTIVE]);
            if(null === $tariff_plan) {
                $tariff_plan = new TariffPlan();
                $tariff_plan->user_id = $account->user_id;
                $tariff_plan->tariff_id = $account->tariff_id;
                $tariff_plan->started_at = $account->tariff_activated_at;
                $tariff_plan->finished_at = $account->tariff_expired_at;
                $tariff_plan->status = TariffPlan::STATUS_ACTIVE;
                $tariff_plan->save(false);
            }
        }
	}

	public function down()
	{
		echo "m131125_131607_update_tariff_plan_from_account migration down.\n";
		return true;
	}

}