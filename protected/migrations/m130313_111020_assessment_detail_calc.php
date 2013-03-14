<?php

class m130313_111020_assessment_detail_calc extends CDbMigration
{
	public function up()
	{
        $this->renameTable('simulations_mail_points', 'assessment_calculation');
        $this->dropColumn('assessment_calculation', 'scale_type_id');
	}

	public function down()
	{
        $this->addColumn('assessment_calculation', 'scale_type_id', 'int');
        $this->renameTable('assessment_calculation', 'simulations_mail_points');
	}
}