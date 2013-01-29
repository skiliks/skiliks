<?php

class m130127_181107_tasks_import_id extends CDbMigration
{
	public function up()
	{
        $this->addColumn('tasks', 'import_id', 'VARCHAR(14) NOT NULL COMMENT \'setvice value,used to remove old data after reimport.\';');
	}

	public function down()
	{
		echo "m130127_181107_tasks_import_id does not support migration down.\n";
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
