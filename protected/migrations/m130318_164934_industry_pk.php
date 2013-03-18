<?php

class m130318_164934_industry_pk extends CDbMigration
{
	public function up()
	{
        $this->delete('industry', 'language="ru"');
        $this->dropColumn('industry', 'language');
        $this->alterColumn('industry', 'id', 'pk');
	}

	public function down()
	{
		echo "m130318_164934_industry_pk does not support migration down.\n";
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