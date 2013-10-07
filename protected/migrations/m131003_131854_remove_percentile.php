<?php

class m131003_131854_remove_percentile extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('simulations', 'percentile');
	}

	public function down()
	{
		echo "m131003_131854_remove_percentile does not support migration down.\n";
		return false;
	}
}