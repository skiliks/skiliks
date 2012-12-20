<?php

class m121212_164839_import_themes extends CDbMigration
{
	public function up()
	{
        $importService = new ImportGameDataService();
        $importService->importEmailSubjects();
        
        $import = new ImportMailPhrases();
        $import->run();
	}

	public function down()
	{
		echo "m121212_164839_import_themes does not support migration down.\n";
		return false;
	}

}