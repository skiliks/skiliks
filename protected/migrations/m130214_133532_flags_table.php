<?php

class m130214_133532_flags_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('flag', array(
            'code' => 'VARCHAR(10) NOT NULL PRIMARY KEY',
            'description' => 'text not null',
            'import_id' => 'VARCHAR(60) NOT NULL'
        ));
	}

	public function down()
	{
        $this->dropTable('flag');
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
