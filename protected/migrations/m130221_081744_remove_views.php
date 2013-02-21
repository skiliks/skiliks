<?php

class m130221_081744_remove_views extends CDbMigration
{
	public function up()
	{
        $this->dropTable('viewer_template');
	}

	public function down()
	{
		echo "m130221_081744_remove_views does not support migration down.\n";
	}
}