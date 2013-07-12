<?php

class m130704_155109_sim_and_invites_logs extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_invite',[
            'id'        => 'pk',
            'invite_id' => 'INT',
            'status'    => 'VARCHAR(40)',
            'sim_id'    => 'INT',
            'action'    => 'TEXT',
            'read_date' => 'DATETIME',
        ]);

        $this->createTable('log_simulation',[
            'id'                 => 'pk',
            'invite_id'          => 'INT',
            'sim_id'             => 'INT',
            'user_id'            => 'INT',
            'scenario_name'      => 'VARCHAR(20)',
            'mode'               => 'VARCHAR(20)',
            'action'             => 'TEXT',
            'read_date'          => 'DATETIME',
            'game_time_frontend' => 'TIME',
            'game_time_backend'  => 'TIME',

        ]);
	}

	public function down()
	{
        $this->dropTable('log_invite');
        $this->dropTable('log_simulation');
	}
}