<?php

class m130528_125552_flag_delay extends CDbMigration
{
	public function up()
	{
        $this->addColumn("flag", 'delay', 'INT(3) DEFAULT 0');
        $this->createTable('simulation_flag_queue', [
            'id'=>'pk',
            'sim_id'=>'int(11) not null',
            'flag_code'=>'varchar(10) not null',
            'delay'=>'int(3) default 0',
            'switch_time'=>'time default null',
            'is_processed'=>'int(3) default 0'
        ]);
        $this->addForeignKey("fk_simulation_flag_queue_sim_id", "simulation_flag_queue", "sim_id", "simulations", "id", 'CASCADE', 'CASCADE');
        $this->addForeignKey("fk_simulation_flag_queue_flag_code", "simulation_flag_queue", "flag_code", "flag", "code", 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropColumn("flag", 'delay');
        $this->dropForeignKey("fk_simulation_flag_queue_sim_id", "simulation_flag_queue");
        $this->dropForeignKey("fk_simulation_flag_queue_flag_code", "simulation_flag_queue");
        $this->dropTable('simulation_flag_queue');
	}
}