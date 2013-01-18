<?php

class m130118_151745_correct_filename_length extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('my_documents_template', 'srcFile', 'string NOT NULL');
	}

	public function down()
	{
		echo "m130118_151745_correct_filename_length does not support migration down.\n";
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
