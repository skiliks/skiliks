<?php

class m130410_141734_update_tariffs_text extends CDbMigration
{
	public function up()
	{
        $this->update('tariff', ['benefits' => 'Бесплатные обновления, 1 месяц — БЕСПЛАТНО'], " slug = 'lite' ");
	}

	public function down()
	{
		echo "m130410_141734_update_tariffs_text does not support migration down.\n";
	}
}