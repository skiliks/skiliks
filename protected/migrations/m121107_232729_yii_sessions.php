<?php

class m121107_232729_yii_sessions extends CDbMigration
{
	public function up()
	{
        $this->createTable('YiiSession', array(
            'id' => 'string NOT NULL PRIMARY KEY',
            'expire' => 'integer',
            'data' => 'BLOB'
        ));
	}

	public function down()
	{
		$this->dropTable('YiiSession');
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