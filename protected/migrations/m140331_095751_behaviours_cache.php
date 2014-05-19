<?php

class m140331_095751_behaviours_cache extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulations', 'behaviours_cache', 'blob default null');
	}

	public function down()
	{
		echo "m140331_095751_behaviours_cache does not support migration down.\n";
		return false;
	}
}