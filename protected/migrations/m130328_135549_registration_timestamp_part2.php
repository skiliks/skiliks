<?php

class m130328_135549_registration_timestamp_part2 extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('profile', 'timestamp');
        $this->addColumn('profile', 'timestamp', 'INT NOT NULL');
	}

	public function down()
	{
		echo "m130328_135549_registration_timestamp_part2 does not support migration down.\n";
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