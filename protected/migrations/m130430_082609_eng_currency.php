<?php

class m130430_082609_eng_currency extends CDbMigration
{
	public function up()
	{
        $this->addColumn('tariff', 'price_usd', 'DECIMAL (10,2) NOT NULL');
        $this->addColumn('tariff', 'safe_amount_usd', 'DECIMAL (10,2) NOT NULL');
        $this->dropColumn('tariff', 'currency');

        $this->update('tariff', ['benefits' => 'Free updates, 1 month FREE', 'price_usd' => 133, 'safe_amount_usd' => 0], 'id = 1');
        $this->update('tariff', ['benefits' => 'Free updates', 'price_usd' => 233, 'safe_amount_usd' => 33], 'id = 2');
        $this->update('tariff', ['benefits' => 'Free updates', 'price_usd' => 498, 'safe_amount_usd' => 167], 'id = 3');
        $this->update('tariff', ['benefits' => 'Free updates', 'price_usd' => 1660, 'safe_amount_usd' => 1000], 'id = 4');
	}

	public function down()
	{
        $this->dropColumn('tariff', 'price_usd');
        $this->dropColumn('tariff', 'safe_amount_usd');
        $this->addColumn('tariff', 'currency', 'VARCHAR(3) NOT NULL DEFAULT \'RUB\'');

        $this->update('tariff', ['benefits' => 'Бесплатные обновления, 1 месяц — БЕСПЛАТНО', 'currency' => 'RUB'], 'id = 1');
        $this->update('tariff', ['benefits' => 'Бесплатные обновления', 'currency' => 'RUB'], 'id = 2');
        $this->update('tariff', ['benefits' => 'Бесплатные обновления', 'currency' => 'RUB'], 'id = 3');
        $this->update('tariff', ['benefits' => 'Бесплатные обновления', 'currency' => 'RUB'], 'id = 4');
	}
}