<?php

class m130401_171842_update_invite_unique extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('fk_invites_owner_id', 'invites');
        $this->dropForeignKey('fk_invites_receiver_id', 'invites');
        $this->dropForeignKey('fk_invites_vacancy_id', 'invites');

        $this->dropIndex('invite_email_unique', 'invites');

        $this->addForeignKey('fk_invites_owner_id', 'invites', 'owner_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_invites_receiver_id', 'invites', 'receiver_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_invites_vacancy_id', 'invites', 'vacancy_id', 'vacancy', 'id', 'SET NULL', 'CASCADE');
	}

	public function down()
	{
        $this->createIndex('invite_email_unique', 'invites', 'inviting_user_id, email', true);
	}
}