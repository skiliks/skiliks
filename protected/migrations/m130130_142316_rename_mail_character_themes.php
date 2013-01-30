<?php

class m130130_142316_rename_mail_character_themes extends CDbMigration
{
	public function up()
    {
        $this->renameTable('mail_character_themes','communication_themes');
	}

	public function down()
	{
		echo "m130130_142316_rename_mail_character_themes does not support migration down.\n";
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
