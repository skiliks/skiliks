<?php

class m121214_165500_import_themes extends CDbMigration
{
	public function up()
	{
            Helper::callAction('MailImportController', 'actionImportThemes');
	}

	public function down()
	{
		//echo "m121212_164839_import_themes does not support migration down.\n";
		//return false;
	}

}