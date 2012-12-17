<?php

class m121217_121151_reimport_mail_templates extends CDbMigration
{
	public function up()
	{
            Helper::callAction('MailImportController', 'actionImport');
	}

	public function down()
	{
		echo "m121217_121151_reimport_mail_templates does not support migration down.\n";
		return false;
	}
}