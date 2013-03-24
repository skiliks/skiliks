<?php

class m130324_003316_fantastic extends CDbMigration
{
	public function up()
	{
        $this->addColumn('replica', 'fantastic_result', 'TINYINT(1) NOT NULL DEFAULT 0');
	}

	public function down()
	{
		$this->dropColumn('replica', 'fantastic_result');
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