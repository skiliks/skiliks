<?php

class m121218_215839_activity_category_null extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('activity', 'category_id', 'integer NOT NULL');
	}

	public function down()
	{
		echo "m121218_215839_activity_category_null does not support migration down.\n";
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