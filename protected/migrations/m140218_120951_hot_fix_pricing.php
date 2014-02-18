<?php

class m140218_120951_hot_fix_pricing extends CDbMigration
{
	public function up()
	{
        $this->update(
            'tariff',
            ['price' => 11900, 'price_usd' => 390,
                'simulations_amount' => 3, 'safe_amount_usd' => 0,
                'safe_amount' => 0, 'benefits' => ''],
            " slug = 'lite' "
        );

        $this->update(
            'tariff',
            ['price' => '34900', 'price_usd' => '990',
                'simulations_amount' => '10', 'safe_amount_usd' => '300',
                'safe_amount' => '5000', 'benefits' => ''],
            " slug = 'starter' "
        );

        $this->update(
            'tariff',
            ['price' => '64900', 'price_usd' => '1790',
                'safe_amount' => '14000', 'safe_amount_usd' => '800',
                'simulations_amount' => '20',
                ],
            " slug = 'professional' "
        );

        $this->update(
            'tariff',
            ['price' => '149900', 'price_usd' => '3990',
                'safe_amount' => '48000', 'safe_amount_usd' => '2500',
                'simulations_amount' => '50',
                ],
            " slug = 'business' "
        );

        $this->update(
            'tariff',
            [
                'simulations_amount' => '0',
            ],
            " slug = 'lite_free' "
        );
	}

	public function down()
	{
		echo "m140218_120951_hot_fix_pricing does not support migration down.\n";
	}
}