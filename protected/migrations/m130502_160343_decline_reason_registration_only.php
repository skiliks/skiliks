<?php

class m130502_160343_decline_reason_registration_only extends CDbMigration
{
	public function up()
	{
        $this->addColumn('decline_reason', 'registration_only', 'tinyint(1) NOT NULL DEFAULT 0');
        $this->update('decline_reason', ['registration_only' => 1], 'alias="dont_want_to_register"');
	}

	public function down()
	{
		echo "m130502_160343_decline_reason_registration_only does not support migration down.\n";
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