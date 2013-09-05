<?php

class m130905_093156_parent_must extends CDbMigration
{
	public function up()
	{
        $this->addColumn('activity_parent_availability', 'must_present_for_214d', 'tinyint(1) default 0');
	}

	public function down()
	{
        $this->dropColumn('activity_parent_availability', 'must_present_for_214d');
	}

}