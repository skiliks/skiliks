<?php

class m121218_221913_reimport_mails extends CDbMigration
{
	public function up()
	{
        $import = new ImportGameDataService();
        $import->importEmails();
        $import->importEmailSubjects();
    }

	public function down()
	{

	}
}