<?php

class m130312_170403_fix_index_assessment_detail extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('fk_assessment_detail_sim_id', 'assessment_detail');
        $this->dropForeignKey('fk_assessment_detail_dialog_id', 'assessment_detail');
        $this->dropForeignKey('fk_assessment_detail_task_id', 'assessment_detail');
        $this->dropForeignKey('fk_assessment_detail_mail_id', 'assessment_detail');
        $this->dropForeignKey('fk_assessment_detail_point_id', 'assessment_detail');

        $this->dropIndex('assessment_detail_unique', 'assessment_detail');

        $this->createIndex('assessment_detail_dialog_unique', 'assessment_detail', 'dialog_id,point_id,sim_id', true);
        $this->createIndex('assessment_detail_task_unique', 'assessment_detail', 'task_id,point_id,sim_id', true);
        $this->createIndex('assessment_detail_mail_unique', 'assessment_detail', 'mail_id,point_id,sim_id', true);

        $this->addForeignKey('fk_assessment_detail_sim_id', 'assessment_detail', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_assessment_detail_dialog_id', 'assessment_detail', 'dialog_id', 'replica', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_assessment_detail_task_id', 'assessment_detail', 'task_id', 'todo', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_assessment_detail_mail_id', 'assessment_detail', 'mail_id', 'mail_box', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_assessment_detail_point_id', 'assessment_detail', 'point_id', 'hero_behaviour', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_assessment_detail_sim_id', 'assessment_detail');
        $this->dropForeignKey('fk_assessment_detail_dialog_id', 'assessment_detail');
        $this->dropForeignKey('fk_assessment_detail_task_id', 'assessment_detail');
        $this->dropForeignKey('fk_assessment_detail_mail_id', 'assessment_detail');
        $this->dropForeignKey('fk_assessment_detail_point_id', 'assessment_detail');

        $this->dropIndex('assessment_detail_dialog_unique', 'assessment_detail');
        $this->dropIndex('assessment_detail_task_unique', 'assessment_detail');
        $this->dropIndex('assessment_detail_mail_unique', 'assessment_detail');

        $this->addForeignKey('fk_assessment_detail_sim_id', 'assessment_detail', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_assessment_detail_dialog_id', 'assessment_detail', 'dialog_id', 'replica', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_assessment_detail_task_id', 'assessment_detail', 'task_id', 'todo', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_assessment_detail_mail_id', 'assessment_detail', 'mail_id', 'mail_box', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_assessment_detail_point_id', 'assessment_detail', 'point_id', 'hero_behaviour', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('assessment_detail_unique', 'assessment_detail', 'dialog_id,task_id,mail_id,point_id,sim_id', true);
	}
}