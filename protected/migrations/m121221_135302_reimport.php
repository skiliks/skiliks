<?php

class m121221_135302_reimport extends CDbMigration
{
	public function up()
	{
        echo "\nReimport dialogs, sample_evelts, character_points \n";
        $service = new DialogImportService();
        $result = $service->import('/../../media/ALL_DIALOGUES.csv');
        
        echo "\nReimport mails, mail_subjects \n";
        $import = new ImportGameDataService();
        $import->importEmails();
        $import->importEmailSubjects();
        
        echo "\nReimport mail phrases \n";
        $import = new ImportMailPhrases();
        $import->run(); 
	}

	public function down()
	{

	}
}