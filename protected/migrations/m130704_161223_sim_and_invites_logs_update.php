<?php

class m130704_161223_sim_and_invites_logs_update extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('log_simulation', 'read_date');

        $this->addColumn('log_invite', 'comment', 'TEXT');
        $this->addColumn('log_simulation', 'comment', 'TEXT');
        $this->addColumn('log_simulation', 'real_date', 'DATETIME');

	}

	public function down()
	{
		$this->dropColumn('log_invite', 'comment');
		$this->dropColumn('log_simulation', 'comment');
	}
}