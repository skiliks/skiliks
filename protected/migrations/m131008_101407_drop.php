<?php

class m131008_101407_drop extends CDbMigration
{
	public function up()
	{
        if (YumUser::model()->hasAttribute('is_check')) {
            $this->dropColumn('user', 'is_check');
        }
	}

	public function down()
	{
		echo "m131008_101407_drop does not support migration down.\n";
		return true;
	}
}