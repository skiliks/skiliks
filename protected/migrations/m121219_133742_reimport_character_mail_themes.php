<?php

class m121219_133742_reimport_character_mail_themes extends CDbMigration
{
	public function up()
	{
        $import = new ImportGameDataService();
        $import->importEmailSubjects();
    }

	public function down()
	{

	}
}