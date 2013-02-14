<?php

class m130213_132053_phone_calls extends CDbMigration
{
	public function up()
	{
        $this->alterColumn("phone_calls", "dialog_id", "VARCHAR (5) DEFAULT NULL");
	}

	public function down()
	{
        $this->alterColumn("phone_calls", "dialog_id", "INT DEFAULT NULL  NULL");
	}

}