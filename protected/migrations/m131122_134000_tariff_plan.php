<?php

class m131122_134000_tariff_plan extends CDbMigration
{
	public function up()
	{
        $this->createTable('tariff_plan', [
            'id'=>'pk',
            'user_id'=>'int(10) unsigned DEFAULT NULL',
            'tariff_id'=>'int(11) DEFAULT NULL',
            'invoice_id'=>'int(11) unsigned DEFAULT NULL',
            'started_at'=>'datetime DEFAULT NULL',
            'finished_at'=>'datetime DEFAULT NULL',
            'status'=>'varchar(15) default null'
        ]);

        $this->addForeignKey('fk_tariff_plan_user_id', 'tariff_plan', 'user_id',
            'user', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk_tariff_plan_tariff_id', 'tariff_plan', 'tariff_id',
            'tariff', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk_tariff_plan_invoice_id', 'tariff_plan', 'invoice_id',
            'invoice', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{

        $this->dropForeignKey('fk_tariff_plan_user_id', 'tariff_plan');

        $this->dropForeignKey('fk_tariff_plan_tariff_id', 'tariff_plan');

        $this->dropForeignKey('fk_tariff_plan_invoice_id', 'tariff_plan');

		$this->dropTable("tariff_plan");
	}
}