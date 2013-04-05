<?php

class m130405_102730_weights extends CDbMigration
{
	public function up()
	{
        $this->createTable('weight', [
            'id' => 'pk',
            'rule_id' => 'int',
            'performance_rule_category_id' => 'varchar(10)',
            'hero_behaviour_id' => 'int',
            'assessment_category_code' => 'varchar(50)',
            'value' => 'decimal(10,6) NOT NULL DEFAULT 0',
            'scenario_id' => 'int',
            'import_id' => 'VARCHAR(14) NOT NULL'
        ]);

        $this->addForeignKey('fk_weight_performance_rule_category_id', 'weight', 'performance_rule_category_id', 'activity_category', 'code', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_weight_hero_behaviour_id', 'weight', 'hero_behaviour_id', 'hero_behaviour', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_weight_assessment_category_code', 'weight', 'assessment_category_code', 'assessment_category', 'code', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_weight_scenario_id', 'weight', 'scenario_id', 'scenario', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_weight_performance_rule_category_id', 'weight');
        $this->dropForeignKey('fk_weight_hero_behaviour_id', 'weight');
        $this->dropForeignKey('fk_weight_assessment_category_code', 'weight');
        $this->dropForeignKey('fk_weight_scenario_id', 'weight');

        $this->dropTable('weight');
	}
}