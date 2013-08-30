<?php

class m130829_171024_add_pk_for_scenario_config extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('scenario_config', 'scenario_id', 'pk');
	}

	public function down()
	{
        echo 'no back mogration';
	}
}