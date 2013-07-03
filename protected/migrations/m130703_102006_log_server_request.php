<?php

class m130703_102006_log_server_request extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_server_request', [
            'id'                =>'pk',
            'sim_id'            =>'int(11) default null',
            'request_uid'       =>'varchar(100) not null',
            'request_url'       =>'varchar(100) not null',
            'request_body'      =>'blob',
            'response_body'     =>'blob',
            'frontend_game_time'=>'time not null',
            'backend_game_time' =>'time not null',
            'real_time'         =>'datetime not null',
            'is_processed'      =>'tinyint not null default 0'
        ]);
        $this->addForeignKey('fk_log_server_request_sim_id', 'log_server_request', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_log_server_request_sim_id', 'log_server_request');
        $this->dropTable('log_server_request');
	}

}