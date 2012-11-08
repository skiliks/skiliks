<?php

class m121108_005114_drop_old_sessions extends CDbMigration
{
	public function up()
	{
        $this->dropTable('users_sessions');
	}

	public function down()
	{
        $this->createTable('users_sessions', array(
            'id' => 'string NOT NULL PRIMARY KEY',
            'user_id' => 'int NOT NULL',
            'start_time' => 'int NOT NULL'
        ));
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