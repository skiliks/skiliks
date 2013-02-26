<?php

class m130226_101913_assessment_rule_condition extends CDbMigration
{
    public function up()
    {
        $this->createTable('assessment_rule_conditions', [
            'id' => 'pk',
            'assessment_rule_id' => 'INT(11) NOT NULL',
            'dialog_id' => 'integer',
            'mail_id' => 'integer'
        ]);

        $this->addForeignKey(
            'fk_assessment_rule_conditions_assessment_rule_id',
            'assessment_rule_conditions',
            'assessment_rule_id',
            'assessment_rules',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_assessment_rule_conditions_dialog_id',
            'assessment_rule_conditions',
            'dialog_id',
            'dialogs',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_assessment_rule_conditions_mail_id',
            'assessment_rule_conditions',
            'mail_id',
            'mail_template',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_assessment_rule_conditions_assessment_rule_id', 'assessment_rule_conditions');
        $this->dropForeignKey('fk_assessment_rule_conditions_dialog_id', 'assessment_rule_conditions');
        $this->dropForeignKey('fk_assessment_rule_conditions_mail_id', 'assessment_rule_conditions');

        $this->dropTable('assessment_rule_conditions');
    }
}