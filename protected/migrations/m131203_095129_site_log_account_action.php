<?php

class m131203_095129_site_log_account_action extends CDbMigration
{
	public function up()
	{
        $this->createTable('site_log_account_action', [
            'id' => 'pk',
            'user_id'=>'int(10) unsigned DEFAULT NULL',
            'ip' => 'varchar(40) default null',
            'message' => 'varchar(255) default null',
            'date' => 'datetime default null'
        ]);


        $this->addForeignKey('fk_site_log_account_action_user_id', 'site_log_account_action', 'user_id',
            'user', 'id', 'CASCADE', 'CASCADE');

        $this->addColumn('user', 'is_password_bruteforce_detected', 'tinyint(1) default 0');

        $this->addColumn('user', 'authorization_after_bruteforce_key', 'varchar(128) default null');
	}

	public function down()
	{
        $this->dropForeignKey('fk_site_log_account_action_user_id', 'site_log_account_action');
        $this->dropTable('site_log_account_action');
        $this->dropColumn('user', 'is_password_bruteforce_detected');
        $this->dropColumn('user', 'authorization_after_bruteforce_key');
	}

}