<?php

class m130318_100041_invite_fix_columns extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('invites', 'sent_time', 'int');
        $this->addForeignKey('fk_invites_position_id', 'invites', 'position_id', 'position', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->alterColumn('invites', 'sent_time', 'timestamp');
        $this->dropForeignKey('fk_invites_position_id', 'invites');
	}
}