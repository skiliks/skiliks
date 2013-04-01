<?php

class m130401_170554_log_replica extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_replica', [
            'id' => 'pk',
            'sim_id' => 'INT NOT NULL',
            'replica_id' => 'INT NOT NULL',
            'time' => 'time NOT NULL'
        ]);

        $this->addForeignKey('fk_log_replica_sim_id', 'log_replica', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_log_replica_replica_id', 'log_replica', 'replica_id', 'replica', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_log_replica_sim_id', 'log_replica');
        $this->dropForeignKey('fk_log_replica_replica_id', 'log_replica');

		$this->dropTable('log_replica');
	}
}