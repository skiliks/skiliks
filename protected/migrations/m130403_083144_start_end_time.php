<?php

class m130403_083144_start_end_time extends CDbMigration
{
	public function up()
	{
        $this->addColumn('scenario', 'start_time', 'time');
        $this->addColumn('scenario', 'end_time', 'time');

        $this->dropColumn('simulations', 'type');
	}

	public function down()
	{
		$this->dropColumn('scenario', 'start_time');
		$this->dropColumn('scenario', 'end_time');

        $this->addColumn('simulations', 'type', 'tinyint(4) NOT NULL DEFAULT 1');
	}
}