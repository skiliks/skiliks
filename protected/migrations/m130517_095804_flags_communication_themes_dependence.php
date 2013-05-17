<?php

class m130517_095804_flags_communication_themes_dependence extends CDbMigration
{
	public function up()
	{
        $this->createTable("flags_communication_themes_dependence", [
            'id'=>'pk',
            'communication_theme_id'=> 'int(11) NOT NULL',
            'flag_code'=>'varchar(10) NOT NULL',
            'scenario_id'=>'int(11) NOT NULL',
            'import_id'=>'varchar(60) NOT NULL'
        ]);
        $this->addForeignKey("fk_communication_theme_id", "flags_communication_themes_dependence", "communication_theme_id", "communication_themes", "id", 'CASCADE', 'CASCADE');
        $this->addForeignKey("fk_flag_code", "flags_communication_themes_dependence", "flag_code", "flag", "code", 'CASCADE', 'CASCADE');
        $this->addForeignKey("fk_scenario_id", "flags_communication_themes_dependence", "scenario_id", "scenario", "id", 'CASCADE', 'CASCADE');

    }

	public function down()
	{
        $this->dropForeignKey("fk_communication_theme_id", "flags_communication_themes_dependence");
        $this->dropForeignKey("fk_flag_code", "flags_communication_themes_dependence");
        $this->dropForeignKey("fk_scenario_id", "flags_communication_themes_dependence");
        $this->dropTable("flags_communication_themes_dependence");
	}

}