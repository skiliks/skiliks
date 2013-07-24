<?php

class m130724_152233_parent extends CDbMigration
{
	public function up()
	{
        $this->addColumn('log_activity_action_agregated_214d', 'parent', 'varchar(10) not null');
	}

	public function down()
	{
		echo "m130724_152233_parent does not support migration down.\n";
		return false;
	}

}