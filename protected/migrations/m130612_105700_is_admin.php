<?php

class m130612_105700_is_admin extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user', 'is_admin', 'INT(1) NOT NULL DEFAULT 0');
	}

	public function down()
	{
        $this->dropColumn('user', 'is_admin');
	}
}