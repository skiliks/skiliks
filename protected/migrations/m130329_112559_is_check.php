<?php

class m130329_112559_is_check extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user', 'is_check', 'TINYINT NOT NULL');
	}

	public function down()
	{
        $this->dropColumn('user', 'is_check');
	}

}