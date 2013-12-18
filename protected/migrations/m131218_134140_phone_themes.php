<?php

class m131218_134140_phone_themes extends CDbMigration
{
	public function up()
	{
        $this->createTable('outgoing_phone_themes',[
            'id' => 'pk',
            'theme_id' => 'int(11) DEFAULT NULL',
            'character_to_id'=>'int(11) DEFAULT NULL',
            'wr' => "varchar(5) default null comment 'Правильная(R), не правильная(W) и нейральная темы(N)'",
            'import_id' => "varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.'",
            'scenario_id' => 'int(11) NOT NULL'
        ]);

        $this->addForeignKey('fk_outgoing_phone_themes_scenario_id', 'outgoing_phone_themes', 'scenario_id',
            'scenario', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_outgoing_phone_themes_theme_id', 'outgoing_phone_themes', 'theme_id',
            'theme', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_outgoing_phone_themes_character_to_id', 'outgoing_phone_themes', 'character_to_id',
            'characters', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_outgoing_phone_themes_scenario_id', 'outgoing_phone_themes');
        $this->dropForeignKey('fk_outgoing_phone_themes_theme_id', 'outgoing_phone_themes');
        $this->dropForeignKey('fk_outgoing_phone_themes_character_to_id', 'outgoing_phone_themes');
        $this->dropTable('outgoing_phone_themes');
	}

}