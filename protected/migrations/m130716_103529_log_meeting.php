<?php

class m130716_103529_log_meeting extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_meeting', [
            'id' => 'pk',
            'sim_id' => 'int not null',
            'meeting_id' => 'int not null',
            'game_time' => 'time not null'
        ]);

        $this->addForeignKey('fk_log_meeting_sim_id', 'log_meeting', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_log_meeting_meeting_id', 'log_meeting', 'meeting_id', 'meeting', 'id', 'CASCADE', 'CASCADE');

        $this->createIndex('log_meeting_unique_record', 'log_meeting', 'sim_id, meeting_id', true);
	}

	public function down()
	{
		$this->dropForeignKey('fk_log_meeting_sim_id', 'log_meeting');
		$this->dropForeignKey('fk_log_meeting_meeting_id', 'log_meeting');

        $this->dropIndex('log_meeting_unique_record', 'log_meeting');

        $this->dropTable('log_meeting');
	}
}