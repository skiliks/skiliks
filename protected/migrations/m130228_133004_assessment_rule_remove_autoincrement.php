<?php

class m130228_133004_assessment_rule_remove_autoincrement extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('assessment_rules', 'id', 'INT NOT NULL');
	}

	public function down()
	{
        $this->alterColumn('assessment_rules', 'id', 'INT NOT NULL AUTO_INCREMENT');
	}
}