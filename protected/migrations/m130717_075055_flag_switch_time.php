<?php

class m130717_075055_flag_switch_time extends CDbMigration
{
	public function up()
	{
        $this->createTable('flag_switch_time', [
            'id' => 'pk',
            'flag_code' => 'varchar(10) not null',
            'value' => 'tinyint(1) default null',
            'time' => 'time default null',
            'scenario_id' => 'int(11) not null',
            'import_id' => 'varchar(14) not null'
        ]);

        //$this->addForeignKey('fk_flag_switch_time_flag_code', 'flag_switch_time', 'flag_code', 'flag', 'code', 'CASCADE', 'CASCADE');
        //$this->addForeignKey('fk_flag_switch_time_scenario_id', 'flag_switch_time', 'scenario_id', 'scenario', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropForeignKey('fk_flag_switch_time_flag_code', 'flag_switch_time');
		$this->dropForeignKey('fk_flag_switch_time_scenario_id', 'flag_switch_time');

        $this->dropTable('flag_switch_time');
	}
}