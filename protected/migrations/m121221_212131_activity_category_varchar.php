<?php

class m121221_212131_activity_category_varchar extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('activity', 'category_id', 'VARCHAR(10)');
	}

	public function down()
	{
		echo "m121221_212131_activity_category_varchar does not support migration down.\n";
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