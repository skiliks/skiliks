<?php

class m140212_144507_add_import_id extends CDbMigration
{
	public function up()
	{
        $this->addColumn('paragraph', 'import_id', "varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.'");
        $this->addColumn('paragraph_pocket', 'import_id', "varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.'");
	}

	public function down()
	{
		echo "m140212_144507_add_import_id does not support migration down.\n";
		return false;
	}
}