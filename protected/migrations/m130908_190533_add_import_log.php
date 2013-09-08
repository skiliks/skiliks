<?php

class m130908_190533_add_import_log extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_import', [
            'id'          => 'pk',
            'user_id'     => 'INT(10) UNSIGNED',
            'scenario_id' => 'INT(11)',
            'started_at'  => 'DATETIME',
            'finished_at' => 'DATETIME',
            'text'        => 'LONGBLOB',
        ]);

        $this->addForeignKey(
            'log_import_fk_user',
            'log_import',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'log_import_fk_scenario',
            'log_import',
            'scenario_id',
            'scenario',
            'id',
            'CASCADE',
            'CASCADE'
        );
	}

	public function down()
	{
        $this->dropTable('log_import');
	}
}