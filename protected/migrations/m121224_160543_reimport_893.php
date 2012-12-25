<?php

class m121224_160543_reimport_893 extends CDbMigration
{
	public function up()
	{
        $importService = new ImportGameDataService();
        
        echo "\nImport Sjbjects: \n";
        $result = $importService->importEmailSubjects();
        var_dump($result);
        
        echo "\nImport Emails: \n";
        $result = $importService->importEmails();
        var_dump($result);
        
        echo "\nImport Dialogs: \n";
        $service = new DialogImportService();
        $result = $service->import('media/ALL_DIALOGUES.csv');
        var_dump($result);
	}

	public function down()
	{
	}
}