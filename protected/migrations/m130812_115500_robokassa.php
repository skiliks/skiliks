<?php

class m130812_115500_robokassa extends CDbMigration
{
	public function up()
	{
        $this->renameColumn('robokassa_transaction','request', 'request_body');
        $this->addColumn('robokassa_transaction', 'request', 'varchar(15) default null');
        $this->addColumn('robokassa_transaction', 'created_at', 'datetime');
        $this->addColumn('robokassa_transaction', 'displayed_at', 'datetime');
        $this->addColumn('robokassa_transaction', 'widget_body', 'text');
        $this->addColumn('robokassa_transaction', 'processed_at', 'datetime');
        $this->addColumn('robokassa_transaction', 'response_body', 'text');
        $this->alterColumn('robokassa_transaction', 'amount', 'decimal(10,4)');
	}

	public function down()
	{
        $this->renameColumn('robokassa_transaction','request_body','request');
        $this->dropColumn('robokassa_transaction', 'request');
        $this->dropColumn('robokassa_transaction', 'created_at');
        $this->dropColumn('robokassa_transaction', 'displayed_at');
        $this->dropColumn('robokassa_transaction', 'widget_body');
        $this->dropColumn('robokassa_transaction', 'processed_at');
        $this->dropColumn('robokassa_transaction', 'response_body');
	}
}