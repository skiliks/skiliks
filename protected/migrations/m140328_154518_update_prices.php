<?php

class m140328_154518_update_prices extends CDbMigration
{
	public function up()
	{
        $priceLite = Price::model()->findByAttributes(['alias' => Price::ALIAS_LITE]);
        $priceStarted = Price::model()->findByAttributes(['alias' => Price::ALIAS_STARTED]);
        $priceProfessional = Price::model()->findByAttributes(['alias' => Price::ALIAS_PROFESSIONAL]);
        $priceBusiness = Price::model()->findByAttributes(['alias' => Price::ALIAS_BUSINESS]);

        $priceLite->in_RUB = 3990;
        $priceLite->in_USD = 130;
        $priceLite->save();

        $priceStarted->in_RUB = 3490;
        $priceStarted->in_USD = 99;
        $priceStarted->save();

        $priceProfessional->in_RUB = 3245;
        $priceProfessional->in_USD = 179/2;
        $priceProfessional->save();

        $priceBusiness->in_RUB = 2990;
        $priceBusiness->in_USD = 399/5;
        $priceBusiness->save();
	}

	public function down()
	{
		echo "m140328_154518_update_prices does not support migration down.\n";
	}
}