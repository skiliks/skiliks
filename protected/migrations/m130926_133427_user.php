<?php

class m130926_133427_user extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('invites','owner_id','int(10) unsigned DEFAULT NULL');
	}

	public function down()
	{
        $this->alterColumn('invites','owner_id','int(10) unsigned NOT NULL');
	}

}