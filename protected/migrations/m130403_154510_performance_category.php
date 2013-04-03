<?php

class m130403_154510_performance_category extends CDbMigration
{
	public function up()
	{
        $this->addColumn('performance_rule', 'category_id', 'VARCHAR(10) DEFAULT NULL');
        $this->addForeignKey('fk_performance_rule_category_id', 'performance_rule', 'category_id', 'activity_category', 'code', 'SET NULL', 'CASCADE');
	}

	public function down()
	{
		$this->dropForeignKey('fk_performance_rule_category_id', 'performance_rule');
        $this->dropColumn('performance_rule', 'category_id');
	}
}