<?php

class m130528_083435_invite_tutorial_update extends CDbMigration
{
	public function up()
	{
        $this->addColumn('invites', 'tutorial_scenario_id', 'int(11) null');
        $this->addColumn('invites', 'tutorial_displayed_at', 'datetime');

        $this->addForeignKey('fk_invites_tutorial_scenario_id', 'invites', 'tutorial_scenario_id', 'scenario', 'id', 'SET NULL', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_invites_tutorial_scenario_id', 'invites');

        $this->dropColumn('invites', 'tutorial_scenario_id');
        $this->dropColumn('invites', 'tutorial_displayed_at');
	}
}