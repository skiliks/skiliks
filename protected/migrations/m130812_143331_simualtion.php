<?php

class m130812_143331_simualtion extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulations', 'user_agent', 'text default null');
        $this->addColumn('simulations', 'screen_resolution', 'varchar(20) default null');
        $this->addColumn('simulations', 'window_resolution', 'varchar(20) default null');
        $this->addColumn('simulations', 'ipv4', 'varchar(20) default null');
        //return false;

	}

	public function down()
	{
        $this->dropColumn('simulations', 'user_agent');
        $this->dropColumn('simulations', 'screen_resolution');
        $this->dropColumn('simulations', 'window_resolution');
        $this->dropColumn('simulations', 'ipv4');
	}

}