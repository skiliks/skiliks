<?php

class m140317_142331_type_pargraph extends CDbMigration
{
	public function up()
	{
        $this->addColumn('paragraph', 'type', 'varchar(20) default null');
	}

	public function down()
	{
        $this->dropColumn('paragraph', 'type');
	}
}