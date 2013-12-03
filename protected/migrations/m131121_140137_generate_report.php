<?php

class m131121_140137_generate_report extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulations', 'assessment_version', 'varchar(10) default null');

    }

	public function down()
	{
        $this->dropColumn('simulations', 'assessment_version');
	}
}