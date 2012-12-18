<?php

class m121214_165500_import_themes extends CDbMigration
{
	public function up()
	{
        $importService = new ImportGameDataService();
        $importService->importEmailSubjects();
	}

	public function down()
	{

	}
}