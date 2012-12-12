<?php

class m121212_164839_import_themes extends CDbMigration
{
	public function up()
	{
            Helper::callAction('MailImportController', 'actionImportThemes');
            Helper::callAction('MailImportController', 'actionImportPhrases');
	}

	public function down()
	{
		echo "m121212_164839_import_themes does not support migration down.\n";
		return false;
	}

}