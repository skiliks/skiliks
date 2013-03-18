<?php

class m130318_152937_position extends CDbMigration
{
	public function up()
	{
        $this->delete('position', 'language="ru"');
        $this->dropColumn('position', 'language');
        $this->alterColumn('position', 'id', 'pk');
	}

	public function down()
	{
		echo "m130318_152937_position does not support migration down.\n";
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