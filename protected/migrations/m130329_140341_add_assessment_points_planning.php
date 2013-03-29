<?php

class m130329_140341_add_assessment_points_planning extends CDbMigration
{
	public function up()
	{
        $this->createTable('assessment_planing_point', [
            'id'                => 'pk',
            'sim_id'            => 'INT NOT NULL',
            'hero_behaviour_id' => 'INT NOT NULL',
            'task_id'           => 'INT NOT NULL',
            'type_scale'        => 'INT NOT NULL',
            'value'             => 'DECIMAL(6,2) NOT NULL',
        ]);

        $this->addForeignKey(
            'assessment_planing_point_planing_fk_simulations',
            'assessment_planing_point',
            'sim_id',
            'simulations',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'assessment_planing_point_fk_hero_behaviour',
            'assessment_planing_point',
            'hero_behaviour_id',
            'hero_behaviour',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'assessment_planing_point_fk_task',
            'assessment_planing_point',
            'task_id',
            'tasks',
            'id',
            'CASCADE',
            'CASCADE'
        );
	}

	public function down()
	{
        $this->dropForeignKey('assessment_planing_point_fk_task', 'assessment_planing_point');
        $this->dropForeignKey('assessment_planing_point_fk_hero_behaviour', 'assessment_planing_point');
        $this->dropForeignKey('assessment_planing_point_fk_simulations', 'assessment_planing_point');

        $this->dropTable('assessment_planing_point');
	}
}