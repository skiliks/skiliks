<?php

class m140320_101343_update_price extends CDbMigration
{
	public function up()
	{
        $price = Price::model()->findByAttributes(['alias'=>'lite']);
        if($price !== null) {
            $price->from = 1;
            $price->save(false);
        }
	}

	public function down()
	{

	}

}