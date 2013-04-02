<?php

class m130402_125028_assessment_group extends CDbMigration
{
	public function up()
	{
        $this->addColumn('assessment_group', 'scenario_id', 'INT(11) NOT NULL');
	}

	public function down()
	{
		echo "m130402_125028_assessment_group does not support migration down.\n";
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