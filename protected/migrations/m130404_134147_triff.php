<?php

class m130404_134147_triff extends CDbMigration
{

	public function safeUp()
	{
        //$this->truncateTable('tariff');
        /* @var Tariff $tariff */
        $tariff = Tariff::model()->findByPk(1);
        $tariff->label = 'Lite';
        $tariff->price = 3990;
        $tariff->safe_amount = 0;
        $tariff->simulations_amount = 10;
        $tariff->benefits = "Бесплатные обновления";
        $tariff->update();
        unset($tariff);

        $tariff = Tariff::model()->findByPk(2);
        $tariff->label = 'Starter';
        $tariff->price = 6980;
        $tariff->safe_amount = 1000;
        $tariff->simulations_amount = 20;
        $tariff->benefits = "Бесплатные обновления";
        $tariff->update();
        unset($tariff);

        $tariff = Tariff::model()->findByPk(3);
        $tariff->label = 'Professional';
        $tariff->price = 14950;
        $tariff->safe_amount = 5000;
        $tariff->simulations_amount = 50;
        $tariff->benefits = "Бесплатные обновления";
        $tariff->update();
        unset($tariff);

        $tariff = Tariff::model()->findByPk(4);
        $tariff->label = 'Business';
        $tariff->price = 49800;
        $tariff->safe_amount = 30000;
        $tariff->simulations_amount = 200;
        $tariff->benefits = "Бесплатные обновления";
        $tariff->update();
        //$this->delete('tariff');


	}

	public function safeDown()
	{
        echo "final";
        return false;
	}
}