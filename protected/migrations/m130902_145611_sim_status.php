<?php

class m130902_145611_sim_status extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulations', 'status', 'varchar(20) default null');
	}

	public function down()
	{
        $this->dropColumn('simulations', 'status');
	}
}