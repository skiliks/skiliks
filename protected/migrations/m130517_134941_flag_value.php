<?php

class m130517_134941_flag_value extends CDbMigration
{
	public function up()
	{
        $this->addColumn("flag_communication_theme_dependence", "value", "INT(1) NOT NULL");
	}

	public function down()
	{
        $this->dropColumn("flag_communication_theme_dependence", "value");
	}

}