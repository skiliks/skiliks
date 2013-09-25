<?php

class m130925_085824_width extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('weight', 'value',"decimal(11,10) NOT NULL DEFAULT '0.0000000000'");
	}

	public function down()
	{
        $this->alterColumn('weight', 'value',"decimal(10,6) NOT NULL DEFAULT '0.000000'");
	}

}