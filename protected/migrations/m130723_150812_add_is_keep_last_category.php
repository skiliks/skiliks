<?php

class m130723_150812_add_is_keep_last_category extends CDbMigration
{
	public function up()
	{
        $this->addColumn('activity_parent_availability', 'is_keep_last_category', 'tinyint(1) default 0');
        $this->dropColumn('activity_parent', 'is_keep_last_category');
	}

	public function down()
	{
		echo "m130723_150812_add_is_keep_last_category does not support migration down.\n";
		return false;
	}

}