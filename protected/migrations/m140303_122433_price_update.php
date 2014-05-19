<?php

class m140303_122433_price_update extends CDbMigration
{
	public function up()
	{
        /* @var $price Price */
        $price = Price::model()->findByAttributes(['alias'=>'lite']);
        $price->in_RUB = 3960;
        $price->in_USD = 130;
        $price->save(false);


        $price = Price::model()->findByAttributes(['alias'=>'started']);
        $price->in_RUB = 3490;
        $price->in_USD = 99;
        $price->save(false);


        $price = Price::model()->findByAttributes(['alias'=>'professional']);
        $price->in_RUB = 3245;
        $price->in_USD = 90;
        $price->save(false);


        $price = Price::model()->findByAttributes(['alias'=>'business']);
        $price->in_RUB = 3000;
        $price->in_USD = 80;
        $price->save(false);
	}

	public function down()
	{

	}

}