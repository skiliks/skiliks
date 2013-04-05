<?php

class m130405_094702_assessment_overall extends CDbMigration
{
	public function up()
	{
        $this->createTable('assessment_category', [
            'code' => 'varchar(50) not null primary key'
        ]);

        $this->insert('assessment_category', ['code' => 'management']);
        $this->insert('assessment_category', ['code' => 'performance']);
        $this->insert('assessment_category', ['code' => 'time']);
        $this->insert('assessment_category', ['code' => 'overall']);

        $this->createTable('assessment_overall', [
            'id' => 'pk',
            'sim_id' => 'int',
            'assessment_category_code' => 'varchar(50)',
            'value' => 'decimal(10,2) NOT NULL DEFAULT 0'
        ]);

        $this->createIndex('assessment_overall_sim_category', 'assessment_overall', 'sim_id, assessment_category_code', true);

        $this->addForeignKey(
            'fk_assessment_overall_sim_id',
            'assessment_overall', 'sim_id',
            'simulations', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk_assessment_overall_assessment_category_code',
            'assessment_overall', 'assessment_category_code',
            'assessment_category', 'code',
            'CASCADE', 'CASCADE'
        );

        $this->dropColumn('simulations', 'managerial_skills');
        $this->dropColumn('simulations', 'managerial_productivity');
        $this->dropColumn('simulations', 'time_management_effectiveness');
        $this->dropColumn('simulations', 'overall_manager_rating');
	}

	public function down()
	{
        $this->addColumn('simulations', 'managerial_skills', 'DECIMAL(10,2) NOT NULL DEFAULT 0');
        $this->addColumn('simulations', 'managerial_productivity', 'DECIMAL(10,2) NOT NULL DEFAULT 0');
        $this->addColumn('simulations', 'time_management_effectiveness', 'DECIMAL(10,2) NOT NULL DEFAULT 0');
        $this->addColumn('simulations', 'overall_manager_rating', 'DECIMAL(10,2) NOT NULL DEFAULT 0');

        $this->dropForeignKey('fk_assessment_overall_sim_id', 'assessment_overall');
        $this->dropForeignKey('fk_assessment_overall_assessment_category_code', 'assessment_overall');

        $this->dropIndex('assessment_overall_sim_category', 'assessment_overall');

		$this->dropTable('assessment_category');
		$this->dropTable('assessment_overall');
	}
}