<?php

class m121129_200239_rename_column extends CDbMigration
{
	public function up()
	{
        $this->renameColumn('YiiCache', 'data', 'value');
	}

	public function down()
	{
		echo "m121129_200239_rename_column does not support migration down.\n";
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