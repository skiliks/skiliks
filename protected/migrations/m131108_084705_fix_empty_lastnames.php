<?php

class m131108_084705_fix_empty_lastnames extends CDbMigration
{
	public function up()
	{
        $this->update('profile',  ['lastname' => 'L.N.'], 'lastname IS NULL');
        $this->update('profile',  ['lastname' => 'L.N.'], "lastname = ''");
	}

	public function down()
	{
		echo "m131108_084705_fix_empty_lastnames does not support migration down.\n";
	}
}