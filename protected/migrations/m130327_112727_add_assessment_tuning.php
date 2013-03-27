<?php

class m130327_112727_add_assessment_tuning extends CDbMigration
{
	public function up()
	{
        $this->addColumn('assessment_aggregated', 'coefficient_for_fixed_value', 'DECIMAL(6,2)');
        $this->addColumn('assessment_aggregated', 'fixed_value', 'DECIMAL(6,2)');
	}

	public function down()
	{
		$this->dropColumn('assessment_aggregated', 'coefficient_for_fixed_value');
		$this->dropColumn('assessment_aggregated', 'fixed_value');
	}
}