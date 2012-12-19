<?php

class m121219_182126_reimport_mail extends CDbMigration
{
	public function up()
	{
        $import = new ImportGameDataService();
        $import->importEmails();
    }

	public function down()
	{

	}
}