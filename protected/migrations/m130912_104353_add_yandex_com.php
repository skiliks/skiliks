<?php

class m130912_104353_add_yandex_com extends CDbMigration
{
	public function up()
	{
        $this->insert('free_email_provider',['domain' => 'yandex.com']);
	}

	public function down()
	{
		echo "m130912_104353_add_yandex_com does not support migration down.\n";
	}
}