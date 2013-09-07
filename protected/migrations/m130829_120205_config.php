<?php

class m130829_120205_config extends CDbMigration
{
	public function up()
	{
        $this->addColumn('scenario_config', 'is_allow_override', 'varchar(250) NOT NULL');
	}

	public function down()
	{
		$this->dropColumn('scenario_config', 'is_allow_override');
	}
}