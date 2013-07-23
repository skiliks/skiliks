<?php

class m130723_162403_add_log_account_invite extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_account_invite',[
            'id' => 'pk',
            'account_id' => 'INT',
            'direction' => 'VARCHAR(10)',
            'amount' => 'INT',
            'limit_after_transaction' => 'INT',
            'comment' => 'TEXT',
            'date' => 'DATETIME',
        ]);
	}

	public function down()
	{
        $this->dropTable('log_account_invite');
	}
}