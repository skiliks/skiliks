<?php

class m130528_080757_flag_to_switch2 extends CDbMigration
{
	public function up()
	{
        $this->addColumn('replica', 'flag_to_switch_2', 'varchar(5) DEFAULT NULL AFTER `flag_to_switch` ');
	}

	public function down()
	{
        $this->dropColumn('replica', 'flag_to_switch_2');
	}

}