<?php

class m130823_130957_duration extends CDbMigration
{
	public function up()
	{
        $this->addColumn('replica', 'duration', 'int(10) default null');
	}

	public function down()
	{
		$this->dropColumn('replica', 'duration');
	}

}