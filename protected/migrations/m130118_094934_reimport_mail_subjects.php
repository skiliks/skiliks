<?php

class m130118_094934_reimport_mail_subjects extends CDbMigration
{
	public function up()
	{
        $importService = new ImportGameDataService();
        $importService->importEmailSubjects();
        
        $importService->importEmails();
	}

	public function down()
	{
        echo "There is no down MySQL.";
	}
}