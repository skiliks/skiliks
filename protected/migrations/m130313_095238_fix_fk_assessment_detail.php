<?php

class m130313_095238_fix_fk_assessment_detail extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('fk_assessment_detail_mail_id', 'assessment_detail');
        $this->addForeignKey('fk_assessment_detail_mail_id', 'assessment_detail', 'mail_id', 'mail_template', 'id', 'CASCADE', 'CASCADE');

        $this->addColumn('assessment_detail', 'value', 'int');

        $this->renameTable('assessment_detail', 'assessment_points');
	}

	public function down()
	{
        $this->renameTable('assessment_points', 'assessment_detail');

        $this->dropForeignKey('fk_assessment_detail_mail_id', 'assessment_detail');
        $this->addForeignKey('fk_assessment_detail_mail_id', 'assessment_detail', 'mail_id', 'mail_box', 'id', 'CASCADE', 'CASCADE');

        $this->dropColumn('assessment_detail', 'value');
	}
}