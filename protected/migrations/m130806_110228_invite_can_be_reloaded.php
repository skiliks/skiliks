<?php

class m130806_110228_invite_can_be_reloaded extends CDbMigration
{
	public function up()
	{
        $this->addColumn('invites', 'can_be_reloaded', 'tinyint(1) default 1');
	}

	public function down()
	{
		$this->dropColumn('invites', 'can_be_reloaded');
	}
}