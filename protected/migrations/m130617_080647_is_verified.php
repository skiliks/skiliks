<?php

class m130617_080647_is_verified extends CDbMigration
{
	public function up()
	{
        $this->addColumn('invoice', 'is_verified', 'int(1) default 0');
	}

	public function down()
	{
        $this->dropColumn('invoice', 'is_verified');
	}

}