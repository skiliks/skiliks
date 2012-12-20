<?php

class m121219_224753_activity_migration extends CDbMigration
{
	public function up()
	{
        $import = new ImportGameDataService();
        $import->importActivity();
	}

	public function down()
	{
		echo "m121219_224753_activity_migration does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}