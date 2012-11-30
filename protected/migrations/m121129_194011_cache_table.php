<?php

class m121129_194011_cache_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('YiiCache', array(
            'id' => 'string NOT NULL PRIMARY KEY',
            'expire' => 'integer',
            'data' => 'BLOB'
        ));
	}

	public function down()
	{
		echo "m121129_194011_cache_table does not support migration down.\n";
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