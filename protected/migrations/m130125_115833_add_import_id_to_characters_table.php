<?php

class m130125_115833_add_import_id_to_characters_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn('characters', 'import_id', 'VARCHAR(14) DEFAULT NULL COMMENT \'setvice value,used to remove old data after reimport.\';');
        
        $importService = new ImportGameDataService();
        $importService->importAll();
	}

	public function down()
	{
		echo "m130125_115833_add_import_id_to_characters_table hasn`t SQL in migration down.\n";
	}
}