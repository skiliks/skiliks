<?php

class m130126_204321_mail_points_import_id extends CDbMigration
{
	public function up()
	{
        $this->addColumn('mail_points', 'import_id', 'VARCHAR(14) DEFAULT NULL COMMENT \'setvice value,used to remove old data after reimport.\';');
	}

	public function down()
	{
		echo "m130126_204321_mail_points_import_id does not support migration down.\n";
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
