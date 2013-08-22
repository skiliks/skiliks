<?php

class m130815_123110_universal_log extends CDbMigration
{
	public function up()
	{
        try {
            $this->dropForeignKey('universal_log_dialog_id', 'universal_log');
        } catch (CDbException $e) {
            // just to run migration
        }

        $this->renameColumn('universal_log', 'dialog_id', 'replica_id');
        $this->addForeignKey('fk_universal_log_replica_id', 'universal_log', 'replica_id', 'replica', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('universal_log', 'fk_universal_log_replica_id');
        $this->renameColumn('universal_log', 'replica_id', 'dialog_id');
        $this->addForeignKey('universal_log_dialog_id', 'universal_log', 'dialog_id', 'replica', 'id', 'CASCADE', 'CASCADE');
	}

}