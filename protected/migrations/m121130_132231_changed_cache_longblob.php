<?php

class m121130_132231_changed_cache_longblob extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('YiiCache', 'value', 'LONGBLOB');
	}

	public function down()
	{
		echo "m121130_132231_changed_cache_longblob does not support migration down.\n";
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