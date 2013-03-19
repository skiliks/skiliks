<?php

class m130319_164207_simulation_type extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('simulations', 'status');
        $this->dropColumn('simulations', 'difficulty');

        $this->renameColumn('simulations', 'type', 'mode');
        $this->addColumn('simulations', 'type', 'TINYINT NOT NULL DEFAULT 1');
	}

	public function down()
	{
        $this->dropColumn('simulations', 'type');
        $this->renameColumn('simulations', 'mode', 'type');

        $this->addColumn('simulations', 'status', 'INT NOT NULL');
        $this->addColumn('simulations', 'difficulty', 'VARCHAR(20) NOT NULL');
	}
}