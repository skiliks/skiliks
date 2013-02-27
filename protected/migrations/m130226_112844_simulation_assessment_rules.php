<?php

class m130226_112844_simulation_assessment_rules extends CDbMigration
{
	public function up()
	{
        $this->createTable('simulation_assessment_rules', [
            'id' => 'pk',
            'sim_id' => 'INT(11) NOT NULL',
            'assessment_rule_id' => 'INT(11) NOT NULL'
        ]);

        $this->addForeignKey(
            'fk_simulation_assessment_rules_assessment_rule_id',
            'simulation_assessment_rules',
            'assessment_rule_id',
            'assessment_rules',
            'id',
            'CASCADE',
            'CASCADE'
        );
	}

	public function down()
	{
        $this->dropForeignKey('fk_simulation_assessment_rules_assessment_rule_id', 'simulation_assessment_rules');
        $this->dropTable('simulation_assessment_rules');
	}
}