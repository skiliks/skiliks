<?php

class m121221_164302_reimport_activities extends CDbMigration
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