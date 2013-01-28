<?php

class m130128_125509_add_migration_id_to_character_points extends CDbMigration
{
	public function up()
	{
        $this->addColumn('characters_points', 'import_id', 'VARCHAR(14) NOT NULL DEFAULT \'00000000000000\' COMMENT \'setvice value,used to remove old data after reimport.\';');
	}

	public function down()
	{
        $this->dropColumn('characters_points', 'import_id');
	}
}