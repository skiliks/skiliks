<?php

class m130407_100451_add_invite_scenario_id extends CDbMigration
{
	public function up()
	{
        $this->addColumn('invites', 'scenario_id', 'INT NOT NULL');
	}

	public function down()
	{
        $this->dropColumn('invites', 'scenario_id');
	}
}