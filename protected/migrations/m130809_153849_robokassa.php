<?php

class m130809_153849_robokassa extends CDbMigration
{
	public function up()
	{
        $this->createTable('robokassa_transaction',[
            'id'=>'pk',
            'user_id'=>'int(10) unsigned NOT NULL',
            'request'=>'text',
            'description'=>'varchar(100)',
            'amount'=>'decimal(10,2)',
            'invoice_id'=>'int(11) NOT NULL'
        ]);
	}

	public function down()
	{
        $this->dropTable('robokassa_transaction');
	}
}