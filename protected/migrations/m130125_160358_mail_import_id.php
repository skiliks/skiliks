<?php

class m130125_160358_mail_import_id extends CDbMigration
{
	public function up()
	{
        $this->addColumn('mail_template', 'import_id', 'VARCHAR(14) DEFAULT NULL COMMENT \'setvice value,used to remove old data after reimport.\';');

        $this->addColumn('mail_character_themes', 'import_id', 'VARCHAR(14) DEFAULT NULL COMMENT \'setvice value,used to remove old data after reimport.\';');

    }

	public function down()
	{
		echo "m130125_160358_mail_import_id does not support migration down.\n";
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