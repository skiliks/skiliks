<?php

class m130408_081549_fix_sound_column extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('replica', 'sound', 'VARCHAR(200)');
	}

	public function down()
	{
		echo "m130408_081549_fix_sound_column does not support migration down.\n";
	}
}