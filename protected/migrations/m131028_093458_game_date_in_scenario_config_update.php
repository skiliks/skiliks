<?php

class m131028_093458_game_date_in_scenario_config_update extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('scenario_config', 'game_date');
        $this->addColumn('scenario_config', 'game_date_text', 'varchar(250) default null');
        $this->addColumn('scenario_config', 'game_date_data', 'varchar(250) default null');
    }

	public function down()
	{
        $this->addColumn('scenario_config', 'game_date', 'varchar(250) default null');
        $this->dropColumn('scenario_config', 'game_date_text');
        $this->dropColumn('scenario_config', 'game_date_data');
    }

}