<?php

class m130226_101904_assessment_rules extends CDbMigration
{
    public function up()
    {
        $this->createTable('assessment_rules', [
            'id' => 'pk',
            'activity_id' => 'VARCHAR(60) NOT NULL',
            'operation' => 'VARCHAR(5)',
            'value' => 'integer'
        ]);

        $this->addForeignKey(
            'fk_assessment_rules_activity_id',
            'assessment_rules',
            'activity_id',
            'activity',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_assessment_rules_activity_id', 'assessment_rules');
        $this->dropTable('assessment_rules');
    }
}