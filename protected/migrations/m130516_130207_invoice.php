<?php

class m130516_130207_invoice extends CDbMigration
{
	public function up()
	{
        $this->createTable('invoice', [
            'id' => 'pk',
            'user_id' => 'int unsigned not null',
            'tariff_id' => 'int not null',
            'status' => 'varchar(20) default "pending"',
            'inn' => 'varchar(50) not null',
            'cpp' => 'varchar(50) not null',
            'account' => 'varchar(50) not null',
            'bic' => 'varchar(50) not null',
            'created_at' => 'datetime not null',
            'updated_at' => 'datetime default null',
            'comment' => 'text'
        ]);

        $this->addForeignKey('fk_invoice_user_id', 'invoice', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_invoice_tariff_id', 'invoice', 'tariff_id', 'tariff', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_invoice_user_id', 'invoice');
        $this->dropForeignKey('fk_invoice_tariff_id', 'invoice');

		$this->dropTable('invoice');
	}
}