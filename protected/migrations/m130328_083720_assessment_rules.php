<?php

class m130328_083720_assessment_rules extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('fk_simulation_assessment_rules_assessment_rule_id', 'simulation_assessment_rules');
        $this->dropForeignKey('fk_assessment_rules_activity_id', 'assessment_rules');
        $this->dropForeignKey('fk_assessment_rule_conditions_assessment_rule_id', 'assessment_rule_conditions');
        $this->dropForeignKey('fk_assessment_rule_conditions_dialog_id', 'assessment_rule_conditions');
        $this->dropForeignKey('fk_assessment_rule_conditions_mail_id', 'assessment_rule_conditions');

        $this->renameTable('assessment_rules', 'performance_rule');
        $this->renameTable('assessment_rule_conditions', 'performance_rule_condition');
        $this->renameTable('simulation_assessment_rules', 'performance_point');

        $this->renameColumn('performance_rule_condition', 'assessment_rule_id', 'performance_rule_id');
        $this->renameColumn('performance_rule_condition', 'dialog_id', 'replica_id');
        $this->renameColumn('performance_point', 'assessment_rule_id', 'performance_rule_id');

        $this->addForeignKey(
            'fk_performance_rule_activity_id',
            'performance_rule', 'activity_id',
            'activity', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_performance_rule_condition_performance_rule_id',
            'performance_rule_condition', 'performance_rule_id',
            'performance_rule', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_performance_rule_condition_replica_id',
            'performance_rule_condition', 'replica_id',
            'replica', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_performance_rule_condition_mail_id',
            'performance_rule_condition', 'mail_id',
            'mail_template', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_performance_point_performance_rule_id',
            'performance_point', 'performance_rule_id',
            'performance_rule', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_performance_point_sim_id',
            'performance_point', 'sim_id',
            'simulations', 'id',
            'CASCADE', 'CASCADE'
        );
	}

	public function down()
	{
        $this->dropForeignKey('fk_performance_rule_activity_id', 'performance_rule');
        $this->dropForeignKey('fk_performance_rule_condition_performance_rule_id', 'performance_rule_condition');
        $this->dropForeignKey('fk_performance_rule_condition_replica_id', 'performance_rule_condition');
        $this->dropForeignKey('fk_performance_rule_condition_mail_id', 'performance_rule_condition');
        $this->dropForeignKey('fk_performance_point_performance_rule_id', 'performance_point');
        $this->dropForeignKey('fk_performance_point_sim_id', 'performance_point');

        $this->renameColumn('performance_rule_condition', 'performance_rule_id', 'assessment_rule_id');
        $this->renameColumn('performance_rule_condition', 'replica_id', 'dialog_id');
        $this->renameColumn('performance_point', 'performance_rule_id', 'assessment_rule_id');

        $this->renameTable('performance_rule', 'assessment_rules');
        $this->renameTable('performance_rule_condition', 'assessment_rule_conditions');
        $this->renameTable('performance_point', 'simulation_assessment_rules');

        $this->addForeignKey(
            'fk_assessment_rules_activity_id',
            'assessment_rules', 'activity_id',
            'activity', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_assessment_rule_conditions_assessment_rule_id',
            'assessment_rule_conditions', 'assessment_rule_id',
            'assessment_rules', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_assessment_rule_conditions_dialog_id',
            'assessment_rule_conditions', 'dialog_id',
            'replica', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_assessment_rule_conditions_mail_id',
            'assessment_rule_conditions', 'mail_id',
            'mail_template', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_simulation_assessment_rules_assessment_rule_id',
            'simulation_assessment_rules', 'assessment_rule_id',
            'assessment_rules', 'id',
            'CASCADE', 'CASCADE'
        );
	}
}