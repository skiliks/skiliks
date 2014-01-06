<?php

class m140106_115509_remove_has_phone_and_mail_theme extends CDbMigration
{
	public function up()
	{
        // это колонки уже неиспользуются
        $this->dropColumn('characters', 'has_mail_theme');
        $this->dropColumn('characters', 'has_phone_theme');
	}

	public function down()
	{
		echo "m140106_115509_remove_has_phone_and_mail_theme does not support migration down.\n";
		return false;
	}
}