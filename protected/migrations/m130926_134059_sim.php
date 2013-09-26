<?php

class m130926_134059_sim extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('simulations','user_id','int(10) unsigned DEFAULT NULL');
	}

	public function down()
	{
        $this->alterColumn('simulations','user_id','int(10) unsigned NOT NULL');
	}

}