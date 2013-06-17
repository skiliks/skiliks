<?php

class m130517_095804_flags_communication_themes_dependence extends CDbMigration
{
	public function up()
	{
        $this->createTable("flag_communication_theme_dependence", [
            'id'=>'pk',
            'communication_theme_id'=> 'int(11) NOT NULL',
            'flag_code'=>'varchar(10) NOT NULL',
            'scenario_id'=>'int(11) NOT NULL',
            'import_id'=>'varchar(60) NOT NULL'
        ]);
        $this->addForeignKey("fk_communication_theme_id", "flag_communication_theme_dependence", "communication_theme_id", "communication_themes", "id", 'CASCADE', 'CASCADE');
        $this->addForeignKey("fk_flag_code", "flag_communication_theme_dependence", "flag_code", "flag", "code", 'CASCADE', 'CASCADE');
        $this->addForeignKey("fk_scenario_id", "flag_communication_theme_dependence", "scenario_id", "scenario", "id", 'CASCADE', 'CASCADE');

    }

	public function down()
	{
        $this->dropForeignKey("fk_communication_theme_id", "flag_communication_theme_dependence");
        $this->dropForeignKey("fk_flag_code", "flag_communication_theme_dependence");
        $this->dropForeignKey("fk_scenario_id", "flag_communication_theme_dependence");
        $this->dropTable("flag_communication_theme_dependence");
	}

}