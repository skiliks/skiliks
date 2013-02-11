<?php

class m130110_225427_phone_calls_call_time extends CDbMigration
{
	public function up()
	{
            $this->addColumn(
            'phone_calls', 
            'call_time', 
            "TIME NOT NULL DEFAULT '00:00:00'");
            $this->dropColumn('phone_calls', 'call_date');
	}

	public function down()
	{
            $this->addColumn(
            'phone_calls', 
            'date_time', 
            "INT(11) DEFAULT NULL");
            $this->dropColumn('phone_calls', 'call_time');
	}
}