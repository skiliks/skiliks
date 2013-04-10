<?php

class m130410_143745_add_phone_call_is_displayed extends CDbMigration
{
	public function up()
	{
        $this->addColumn('phone_calls', 'is_displayed', 'TINYINT(1) DEFAULT 0');
	}

	public function down()
	{
        $this->dropColumn('phone_calls', 'is_displayed');
	}
}