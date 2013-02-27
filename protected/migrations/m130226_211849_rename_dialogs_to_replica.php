<?php

class m130226_211849_rename_dialogs_to_replica extends CDbMigration
{
	public function up()
	{
        $this->renameTable('dialogs', 'replica');
	}

	public function down()
	{
		echo "m130226_211849_rename_dialogs_to_replica does not support migration down.\n";
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