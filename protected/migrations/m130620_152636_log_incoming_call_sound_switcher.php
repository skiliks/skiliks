<?php

class m130620_152636_log_incoming_call_sound_switcher extends CDbMigration
{
	public function up()
	{
        $this->createTable("log_incoming_call_sound_switcher", [
            'id'=>'pk',
            'sim_id' => 'int(11) NOT NULL',
            'is_play' => 'int(1) NOT NULL',
            'sound_alias' => 'varchar(50) NOT NULL',
            'game_time' => 'time NOT NULL',
        ]);
        $this->addForeignKey('fk_log_incoming_call_sound_switcher_sim_id', 'log_incoming_call_sound_switcher', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropTable('log_incoming_call_sound_switcher');
        $this->dropForeignKey('fk_log_incoming_call_sound_switcher_sim_id', 'log_incoming_call_sound_switcher');
	}

}