<?php

class m130118_092249_cleanup_cheracter_subjects extends CDbMigration
{
	public function up()
	{
        $this->getDbConnection()
            ->createCommand("DELETE FROM `mail_character_themes` WHERE `character_id` IS NULL;")
            ->execute();
	}

	public function down()
	{
        echo "There is no down MySQL.";
	}
}