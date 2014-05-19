<?php

class m140303_163054_invoice_update extends CDbMigration
{
	public function up()
	{
        $this->addColumn('invoice', 'simulation_selected', 'int(11) default null');
	}

	public function down()
	{
        $this->dropColumn('invoice', 'simulation_selected');
	}

}