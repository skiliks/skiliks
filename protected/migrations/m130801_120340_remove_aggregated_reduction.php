<?php

class m130801_120340_remove_aggregated_reduction extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('assessment_aggregated', 'coefficient_for_fixed_value');
        $this->dropColumn('assessment_aggregated', 'fixed_value');
	}

	public function down()
	{
		$this->addColumn('assessment_aggregated', 'coefficient_for_fixed_value', 'decimal(6,2) default null');
		$this->addColumn('assessment_aggregated', 'fixed_value', 'decimal(6,2) default null');
	}
}