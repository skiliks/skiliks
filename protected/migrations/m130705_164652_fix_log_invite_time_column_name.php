<?php

class m130705_164652_fix_log_invite_time_column_name extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('log_invite', 'read_date');

        $this->addColumn('log_invite', 'real_date', 'DATETIME');
	}

	public function down()
	{
        $this->dropColumn('log_invite', 'reaj_date');

        $this->addColumn('log_invite', 'read_date', 'DATETIME');
	}
}