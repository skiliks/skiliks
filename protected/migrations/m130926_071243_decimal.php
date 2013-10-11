<?php

class m130926_071243_decimal extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('performance_aggregated', 'percent', "decimal(10,6) NOT NULL DEFAULT '0.000000'");
	}

	public function down()
	{
        $this->alterColumn('performance_aggregated', 'percent', "int(11) DEFAULT NULL");
	}

}