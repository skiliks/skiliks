<?php

class m130320_215852_add_invites_counter extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_corporate', 'invites_limit', 'INT(4) UNSIGNED NOT NULL DEFAULT 0');

        $this->update('user_account_corporate', ['invites_limit' => 10]); // for testing
	}

	public function down()
	{
        $this->dropColumn('user_account_corporate', 'invites_limit');
	}
}