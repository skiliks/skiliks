<?php

class m130328_095946_remove_efficiency extends CDbMigration
{
	public function up()
	{
        $this->dropTable('activity_efficiency_conditions');
	}

	public function down()
	{
		echo "m130328_095946_remove_efficiency does not support migration down.\n";
		return false;
	}
}