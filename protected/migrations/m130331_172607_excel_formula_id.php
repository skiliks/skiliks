<?php

class m130331_172607_excel_formula_id extends CDbMigration
{
	public function up()
	{
        $this->addColumn('performance_rule_condition', 'excel_formula_id', 'INT(11) NULL DEFAULT NULL');
        $this->addForeignKey('fk_performance_rule_condition_excel_formula_id', 'performance_rule_condition', 'excel_formula_id',
            'excel_points_formula', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_performance_rule_condition_excel_formula_id', 'performance_rule_condition');
        $this->dropColumn('performance_rule_condition', 'excel_formula_id');
	}

}