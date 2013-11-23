<?php

class m131122_134058_tariff_update extends CDbMigration
{
	public function up()
	{
        $this->addColumn('tariff', 'weight', 'tinyint default null');
        $this->addColumn('tariff', 'is_display_on_tariffs_page', 'tinyint default 0');

        $transaction = $this->dbConnection->beginTransaction();

        try{
            $tariff = new Tariff();
            $tariff->label = 'Free';
            $tariff->is_free = 1;
            $tariff->price = 0;
            $tariff->safe_amount = 0;
            $tariff->simulations_amount = 0;
            $tariff->description = '';
            $tariff->benefits = 'Free updates';
            $tariff->slug = 'free';
            $tariff->price_usd = 0;
            $tariff->safe_amount_usd = 0;
            $tariff->weight = 0;
            $tariff->is_display_on_tariffs_page = 0;
            $tariff->save(false);

            $tariff = new Tariff();
            $tariff->label = 'LiteFree';
            $tariff->is_free = 1;
            $tariff->price = 0;
            $tariff->safe_amount = 0;
            $tariff->simulations_amount = 3;
            $tariff->description = '';
            $tariff->benefits = 'Free updates';
            $tariff->slug = 'lite_free';
            $tariff->price_usd = 0;
            $tariff->safe_amount_usd = 0;
            $tariff->weight = 1;
            $tariff->is_display_on_tariffs_page = 0;
            $tariff->save(false);

            $tariff = Tariff::model()->findByAttributes(['slug'=>'lite']);
            $tariff->weight = 2;
            $tariff->is_display_on_tariffs_page = 1;
            $tariff->save(false);

            $tariff = Tariff::model()->findByAttributes(['slug'=>'starter']);
            $tariff->weight = 3;
            $tariff->is_display_on_tariffs_page = 1;
            $tariff->save(false);

            $tariff = Tariff::model()->findByAttributes(['slug'=>'professional']);
            $tariff->weight = 4;
            $tariff->is_display_on_tariffs_page = 1;
            $tariff->save(false);

            $tariff = Tariff::model()->findByAttributes(['slug'=>'business']);
            $tariff->weight = 5;
            $tariff->is_display_on_tariffs_page = 1;
            $tariff->save(false);

            $accounts = UserAccountCorporate::model()->findAll("tariff_expired_at <= :tariff_expired_at", ['tariff_expired_at'=>(new DateTime())->format("Y-m-d H:i:s")]);
            foreach($accounts as $account){
                /* @var $account UserAccountCorporate */
                $tariff = Tariff::model()->findByAttributes(['slug'=>'free']);
                $account->setTariff($tariff, true);
            }

            $users = YumUser::model()->findAll("createtime >= :createtime", ['createtime'=>strtotime("-30 days")]);
            foreach($users as $user){
                /* @var $user YumUser */
                if($user->isCorporate()) {
                    if($user->isBanned()) {
                        $tariff = Tariff::model()->findByAttributes(['slug'=>Tariff::SLUG_FREE]);
                        $user->account_corporate->setTariff($tariff, true);
                    } else {
                        $invites_limit = $user->account_corporate->invites_limit;
                        $tariff = Tariff::model()->findByAttributes(['slug'=>Tariff::SLUG_LITE_FREE]);
                        $user->account_corporate->setTariff($tariff, true);
                        $user->account_corporate->invites_limit = $invites_limit;
                        $user->account_corporate->save(false);
                    }
                }
            }
            $transaction->commit();
        } catch (Exception $e){
            $transaction->rollback();
            return false;
        }
        return true;
	}

	public function down()
	{
		echo "m131122_134058_tariff_update does not support migration down.\n";
		return true;
	}
}