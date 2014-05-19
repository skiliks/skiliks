<?php

class m140324_112624_add_is_test_payment_to_order extends CDbMigration
{
	public function up()
	{
        $this->addColumn('invoice', 'is_test_payment', 'TINYINT(1) DEFAULT 0');
	}

	public function down()
	{
        $this->dropColumn('invoice', 'is_test_payment');
	}
}