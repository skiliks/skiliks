<?php

class m130513_124010_has_themes extends CDbMigration
{
	public function up()
	{
        $this->addColumn('characters', 'has_mail_theme', "INT(1) NOT NULL DEFAULT 0");
        $this->addColumn('characters', 'has_phone_theme', "INT(1) NOT NULL DEFAULT 0");
	}

	public function down()
	{
        $this->dropColumn('characters', 'has_phone_theme');
        $this->dropColumn('characters', 'has_mail_theme');
	}
}