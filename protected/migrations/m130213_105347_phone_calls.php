<?php

class m130213_105347_phone_calls extends CDbMigration
{
	public function up()
	{
        $this->addColumn("phone_calls", "dialog_id", "INT DEFAULT NULL  NULL");
	}

	public function down()
	{
        $this->dropColumn("phone_calls", "dialog_id");
	}

}