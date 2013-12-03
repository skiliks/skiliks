<?php

class m131126_154031_update_tariff_plan_id extends CDbMigration
{
	public function up()
	{
        $invites = Invite::model()->findAll("status != :expired and status != :deleted", ['expired'=>Invite::STATUS_EXPIRED, 'deleted'=>Invite::STATUS_DELETED]);
        foreach($invites as $invite){
            /* @var Invite $invite */
            if($invite->ownerUser !== null){
                $account = $invite->ownerUser->account_corporate;
                if(null !== $account) {
                    $tariff = $account->getActiveTariff();
                    if(null !== $tariff) {
                        if($tariff->slug !== Tariff::SLUG_FREE) {
                            $invite->setTariffPlan();
                            $invite->save(false);
                        }
                    }
                }
            }

        }
	}

	public function down()
	{
		echo "m131126_154031_update_tariff_plan_id does not support migration down.\n";
		return false;
	}
}