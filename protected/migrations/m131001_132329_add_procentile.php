<?php

class m131001_132329_add_procentile extends CDbMigration
{
	public function up()
	{
        $this->addColumn("simulations", "percentile", "DECIMAL (3,2) DEFAULT NULL");
	}

	public function down()
	{
        $this->dropColumn("simulations", "percentile");
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