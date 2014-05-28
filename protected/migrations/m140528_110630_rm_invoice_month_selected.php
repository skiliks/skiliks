<?php

class m140528_110630_rm_invoice_month_selected extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('invoice', 'month_selected');
	}

	public function down()
	{
		echo "m140528_110630_rm_invoice_month_selected does not support migration down.\n";
	}
}