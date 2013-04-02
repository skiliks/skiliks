<?php

class m130331_193128_performance_rule_code extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('performance_rule', 'id', 'int not null auto_increment');
        $this->addColumn('performance_rule', 'code', 'int NOT NULL');
        $this->createIndex('performance_rule', 'performance_rule', 'code, scenario_id', true);
	}

	public function down()
	{
		echo "m130331_193128_performance_rule_code does not support migration down.\n";
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