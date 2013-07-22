<?php

class m130722_214159_add_invite_tutorial_finished_at extends CDbMigration
{
	public function up()
	{
        $this->addColumn('invites', 'tutorial_finished_at', 'DATETIME DEFAULT NULL');
	}

	public function down()
	{
        $this->dropColumn('invites', 'tutorial_finished_at');
	}
}