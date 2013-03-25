<?php

class m130325_131147_invite_vacancy_link extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('fk_invites_position_id', 'invites');
        $this->renameColumn('invites', 'position_id', 'vacancy_id');
        $this->addForeignKey('fk_invites_vacancy_id', 'invites', 'vacancy_id', 'vacancy', 'id', 'SET NULL', 'CASCADE');

        $this->dropForeignKey('fk_invites_inviting_user_id', 'invites');
        $this->dropForeignKey('fk_invites_invited_user_id', 'invites');

        $this->renameColumn('invites', 'inviting_user_id', 'owner_id');
        $this->renameColumn('invites', 'invited_user_id', 'receiver_id');

        $this->addForeignKey('fk_invites_owner_id', 'invites', 'owner_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_invites_receiver_id', 'invites', 'receiver_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        return false;
	}
}