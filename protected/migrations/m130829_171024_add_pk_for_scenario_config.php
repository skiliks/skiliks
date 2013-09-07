<?php

class m130829_171024_add_pk_for_scenario_config extends CDbMigration
{
	public function up()
	{
        $this->execute('SET FOREIGN_KEY_CHECKS=0');
        $this->alterColumn('scenario_config', 'scenario_id', 'pk');
        $this->execute('SET FOREIGN_KEY_CHECKS=1');
	}

	public function down()
	{
        echo 'no back mogration';
	}
}