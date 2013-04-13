<?php

class m130413_193247_fix_assessment_planing_point extends CDbMigration
{
	public function up()
	{

        $this->alterColumn('assessment_planing_point', 'task_id', 'INT DEFAULT NULL');
        $this->addColumn('assessment_planing_point', 'activity_parent_code', 'VARCHAR(20) DEFAULT NULL');
	}

	public function down()
	{
        $this->dropColumn('assessment_planing_point', 'activity_parent_code');
	}
}