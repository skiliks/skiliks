<?php

class m130320_144004_sim_pause extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulations', 'paused', 'datetime DEFAULT NULL');
        $this->addColumn('simulations', 'skipped', 'int DEFAULT 0');
	}

	public function down()
	{
        $this->dropColumn('simulations', 'paused');
        $this->dropColumn('simulations', 'skipped');
	}
}