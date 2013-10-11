<?php

class m131002_225247_fix_percentile extends CDbMigration
{
	public function up()
	{
        $this->alterColumn("simulations", "percentile", "DECIMAL (5,2) DEFAULT NULL");
	}

	public function down()
	{
		echo "m131002_225247_fix_percentile does not support migration down.\n";
		return false;
	}
}