<?php

class m140303_111346_price_update extends CDbMigration
{
	public function up()
	{

        $price = new Price();
        $price->name = 'Lite';
        $price->alias = 'lite';
        $price->from = 3;
        $price->to = 10;
        $price->in_RUB = 11900;
        $price->in_USD = 390;
        $price->save(false);

        $price = new Price();
        $price->name = 'Started';
        $price->alias = 'started';
        $price->from = 10;
        $price->to = 20;
        $price->in_RUB = 34900;
        $price->in_USD = 990;
        $price->save(false);

        $price = new Price();
        $price->name = 'Professional';
        $price->alias = 'professional';
        $price->from = 20;
        $price->to = 50;
        $price->in_RUB = 64900;
        $price->in_USD = 1790;
        $price->save(false);

        $price = new Price();
        $price->name = 'Business';
        $price->alias = 'business';
        $price->from = 50;
        $price->to = 1000000;
        $price->in_RUB = 149900;
        $price->in_USD = 3990;
        $price->save(false);
	}

	public function down()
	{

	}

}