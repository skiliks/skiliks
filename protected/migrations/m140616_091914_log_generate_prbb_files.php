<?php

class m140616_091914_log_generate_prbb_files extends CDbMigration
{
	public function up()
	{
        $this->createTable(
            'site_log_generate_prbb_files',
            [
                'id'            => 'pk',
                'started_at'    => 'datetime',
                'finished_at'   => 'datetime',
                'started_by_id' => 'int(10) UNSIGNED',
                'path' => 'TEXT',
                'result'        => 'LONGTEXT',
            ]
        );

        $this->addForeignKey('site_log_generate_prbb_files_fk_user',
            'site_log_generate_prbb_files', 'started_by_id',
            'user', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('site_log_generate_prbb_files_fk_user', 'site_log_generate_prbb_files');
        $this->dropTable('site_log_generate_prbb_files');
	}
}