<?php

class m140519_173926_add_check_results_table extends CDbMigration
{
	public function up()
	{
        $this->createTable(
            'site_log_check_results',
            [
                'id'            => 'pk',
                'started_at'    => 'datetime',
                'finished_at'   => 'datetime',
                'started_by_id' => 'int(10) UNSIGNED',
                'result'        => 'blob',
            ]
        );

        $this->addForeignKey('site_log_check_results_fk_user', 'site_log_check_results', 'started_by_id',
            'user', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('site_log_check_results_fk_user', 'site_log_check_results');
        $this->dropTable('site_log_check_results');
	}
}