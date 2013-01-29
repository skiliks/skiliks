<?php

class m130129_125735_reimport extends CDbMigration
{
	public function up()
	{
        $s = new ImportGameDataService();
        $s->importAll();
	}

	public function down()
	{
		echo "m130129_125735_reimport does not support migration down.\n";
	}
}