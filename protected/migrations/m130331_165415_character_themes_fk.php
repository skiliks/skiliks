<?php

class m130331_165415_character_themes_fk extends CDbMigration
{
	public function up()
	{
        $this->addForeignKey('communication_themes_characher', 'communication_themes', 'character_id', 'characters', 'id');
        $this->createIndex('communication_theme_uniq', 'communication_themes', 'code, mail_prefix, character_id, theme_usage, scenario_id', true);
	}

	public function down()
	{
        $this->dropIndex('communication_theme_uniq', 'communication_themes');
        $this->dropForeignKey('communication_themes_characher', 'communication_themes');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}