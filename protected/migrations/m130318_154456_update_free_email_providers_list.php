<?php

class m130318_154456_update_free_email_providers_list extends CDbMigration
{
	public function up()
	{
        $this->insert('free_email_provider', ['domain' => 'inbox.ru']);
        $this->insert('free_email_provider', ['domain' => 'bk.ru']);
        $this->insert('free_email_provider', ['domain' => 'list.ru']);
	}

	public function down()
	{
		echo "m130318_154456_update_free_email_providers_list does not support migration down.\n";
	}
}