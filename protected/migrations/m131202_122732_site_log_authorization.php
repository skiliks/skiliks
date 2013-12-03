<?php

class m131202_122732_site_log_authorization extends CDbMigration
{
	public function up()
	{
        $this->createTable("site_log_authorization", [
            'id'=>'pk',
            'ip'=>'varchar(30) default null',
            'is_success'=>'tinyint(1) default null',
            'user_agent'=>'VARCHAR(255) default null',
            'date'=>'datetime default null',
            'login'=>'varchar(255) default null',
            'password'=>'varchar(255) default null',
            'referral_url'=>'text default null',
            'user_id'=>'int(10) unsigned DEFAULT NULL',
            'type_auth'=>'varchar(20) default null'
        ]);


        $this->addForeignKey('fk_site_log_authorization_user_id', 'site_log_authorization', 'user_id',
            'user', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_site_log_authorization_user_id', 'site_log_authorization');
        $this->dropTable('site_log_authorization');
	}

}