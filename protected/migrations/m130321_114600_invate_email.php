<?php

class m130321_114600_invate_email extends CDbMigration
{
	public function up()
	{
        $this->createIndex('invite_email_unique', 'invites', 'inviting_user_id, email', true);
	}

	public function down()
	{
		$this->dropIndex('invite_email_unique', 'invites');
	}

}