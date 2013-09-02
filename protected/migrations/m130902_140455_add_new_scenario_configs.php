<?php

class m130902_140455_add_new_scenario_configs extends CDbMigration
{
	public function up()
	{
        $this->addColumn('scenario_config', 'scenario_label_text', 'VARCHAR(120)');
        $this->addColumn('scenario_config', 'scenario_label_image', 'VARCHAR(120)');
	}

	public function down()
	{
        $this->dropColumn('scenario_config', 'scenario_label_text');
        $this->dropColumn('scenario_config', 'scenario_label_image');
	}
}