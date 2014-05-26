<?php

class m140526_132412_add_sie_logs_generate_consolidated_analytic_file extends CDbMigration
{
    public function up()
    {
        $this->createTable(
            'site_log_generate_consolidated_analytic_file',
            [
                'id'            => 'pk',
                'started_at'    => 'datetime',
                'finished_at'   => 'datetime',
                'started_by_id' => 'int(10) UNSIGNED',
                'result'        => 'LONGTEXT',
            ]
        );

        $this->addForeignKey('site_log_generate_consolidated_analytic_file_fk_user',
            'site_log_generate_consolidated_analytic_file', 'started_by_id',
            'user', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('site_log_generate_consolidated_analytic_file_fk_user', 'site_log_check_results');
        $this->dropTable('site_log_generate_consolidated_analytic_file');
    }
}