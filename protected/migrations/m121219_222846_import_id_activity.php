<?php

class m121219_222846_import_id_activity extends CDbMigration
{
	public function up()
	{
        $this->addColumn('activity', 'import_id', 'string NOT NULL');
        $this->addColumn('activity_action', 'import_id', 'string NOT NULL');
	}

	public function down()
	{
        $this->dropColumn('activity', 'import_id');
        $this->dropColumn('activity_action', 'import_id');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}