<?php

class m140321_121308_add_short_text extends CDbMigration
{
	public function up()
	{
        $this->addColumn('paragraph_pocket', 'short_text', 'varchar(250) default null');
	}

	public function down()
	{

	}
}