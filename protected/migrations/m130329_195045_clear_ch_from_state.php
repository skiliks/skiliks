<?php

class m130329_195045_clear_ch_from_state extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('replica', 'ch_from_state');
        $this->dropColumn('replica', 'ch_to_state');
	}

	public function down()
	{
		echo "m130329_195045_clear_ch_from_state does not support migration down.\n";
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