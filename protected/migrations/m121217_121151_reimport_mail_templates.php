<?php

class m121217_121151_reimport_mail_templates extends CDbMigration
{
	public function up()
	{
        $importService = new ImportGameDataService();
        $result = $importService->importEmails();
	}

	public function down()
	{
		echo "m121217_121151_reimport_mail_templates does not support migration down.\n";
		return false;
	}
}