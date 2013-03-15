<?php

class m130315_105612_invites extends CDbMigration
{
	public function up()
	{
        $this->createTable('invites', [
            'id'                => 'pk',
            'inviting_user_id'  => 'INT UNSIGNED NOT NULL',
            'invited_user_id'   => 'INT UNSIGNED NULL',
            'firstname'         => 'varchar(100)',
            'lastname'          => 'varchar(100)',
            'email'             => 'varchar(255)',
            'message'           => 'text',
            'signature'         => 'varchar(255)',
            'code'              => 'varchar(50)',
        ]);

        $this->createIndex('invites_code_unique', 'invites', 'code', true);

        $this->addForeignKey('fk_invites_inviting_user_id', 'invites', 'inviting_user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_invites_invited_user_id', 'invites', 'invited_user_id', 'user', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_invites_inviting_user_id', 'invites');
        $this->dropForeignKey('fk_invites_invited_user_id', 'invites');
        $this->dropIndex('invites_code_unique', 'invites');

		$this->dropTable('invites');
	}
}