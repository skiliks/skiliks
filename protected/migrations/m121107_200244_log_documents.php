<?php

class m121107_200244_log_documents extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_documents', array(
            'id'         => 'pk',
            'sim_id'     => 'INT(11) NOT NULL',
            'file_id'    => 'INT(11) NOT NULL',
            'start_time' => 'TIME NOT NULL',
            'end_time'   => "TIME NOT NULL DEFAULT '00:00:00'"
        ));

	}

	public function down()
	{
        $this->dropTable('log_documents');
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