<?php

class m130227_105542_import_id_for_assessment_rules extends CDbMigration
{
	public function up()
	{
        $this->addColumn('assessment_rules', 'import_id', 'VARCHAR(14)');
        $this->addColumn('assessment_rule_conditions', 'import_id', 'VARCHAR(14)');
	}

	public function down()
	{
		$this->dropColumn('assessment_rules', 'import_id');
		$this->dropColumn('assessment_rule_conditions', 'import_id');
	}
}