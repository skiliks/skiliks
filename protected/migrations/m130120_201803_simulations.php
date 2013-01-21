<?php

class m130120_201803_simulations extends CDbMigration
{
	public function up()
	{
        $this->delete('simulations');
        $this->alterColumn('simulations', 'start', 'datetime DEFAULT NULL');
        $this->alterColumn('simulations', 'end', 'datetime DEFAULT NULL');
	}

	public function down()
	{
        $this->alterColumn('simulations', 'start', 'int(11) NOT NULL');
        $this->alterColumn('simulations', 'end', 'int(11) NOT NULL');
	}

}