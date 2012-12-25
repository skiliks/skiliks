<?php

class m121221_124936_add_window_id extends CDbMigration
{
	public function up()
	{
        $this->addColumn('activity_action', 'window_id', 'integer');
        #$this->addForeignKey('fk_activity_action', 'activity_action', 'window_id')
	}

	public function down()
	{
		echo "m121221_124936_add_window_id does not support migration down.\n";
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