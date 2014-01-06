<?php

class m131218_134053_mail_themes extends CDbMigration
{
	public function up()
	{
        $this->createTable('outbox_mail_themes',[
            'id' => 'pk',
            'theme_id' => 'int(11) DEFAULT NULL',
            'character_to_id'=>'int(11) DEFAULT NULL',
            'mail_constructor_id' => 'int(11) DEFAULT NULL',
            'import_id' => "varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.'",
            'scenario_id' => 'int(11) NOT NULL'
        ]);

        $this->addForeignKey('fk_outbox_mail_themes_scenario_id', 'outbox_mail_themes', 'scenario_id',
            'scenario', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_outbox_mail_themes_theme_id', 'outbox_mail_themes', 'theme_id',
            'theme', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_outbox_mail_themes_mail_constructor_id', 'outbox_mail_themes', 'mail_constructor_id',
            'mail_constructor', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_outbox_mail_themes_character_to_id', 'outbox_mail_themes', 'character_to_id',
            'characters', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_outbox_mail_themes_scenario_id', 'outbox_mail_themes');
        $this->dropForeignKey('fk_outbox_mail_themes_theme_id', 'outbox_mail_themes');
        $this->dropForeignKey('fk_outbox_mail_themes_mail_constructor_id', 'outbox_mail_themes');
        $this->dropForeignKey('fk_outbox_mail_themes_character_to_id', 'outbox_mail_themes');
        $this->dropTable('outbox_mail_themes');
	}

}