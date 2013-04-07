<?php

class m130407_082443_add_tariff_slug extends CDbMigration
{
	public function up()
	{
        $this->addColumn('tariff', 'slug', 'VARCHAR(20) NOT NULL');

        $this->update('tariff', ['slug' => 'lite'], " label = 'Lite' ");
        $this->update('tariff', ['slug' => 'starter'], " label = 'Starter' ");
        $this->update('tariff', ['slug' => 'professional'], " label = 'Professional' ");
        $this->update('tariff', ['slug' => 'business'], " label = 'Business' ");
	}

	public function down()
	{
        $this->dropColumn('tariff', 'slug');
	}

}