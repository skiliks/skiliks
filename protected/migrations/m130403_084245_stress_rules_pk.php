<?php

class m130403_084245_stress_rules_pk extends CDbMigration
{
	public function up()
	{
        $this->addColumn('stress_rule', 'code', 'INT(11) NOT NULL');
        $this->addColumn('stress_rule', 'scenario_id', 'INT(11) NOT NULL');
        $this->addForeignKey('fk_stress_rule_scenario_id', 'stress_rule', 'scenario_id', 'scenario', 'id', 'cascade', 'cascade');
        $this->createIndex('stress_rule_uniq', 'stress_rule', 'code, scenario_id', true);
	}

	public function down()
	{
		echo "m130403_084245_stress_rules_pk does not support migration down.\n";
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