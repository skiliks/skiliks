<?php

class m121221_202014_reimport_activities extends CDbMigration
{
	public function up()
	{
        $import = new ImportGameDataService();
        $import->importActivity();
	}

	public function down()
	{

	}
}