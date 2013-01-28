<?php

class m130128_100526_add_import_id_to_dialogs extends CDbMigration
{
	public function up()
	{
        $this->addColumn('dialogs', 'import_id', 'VARCHAR(14) NOT NULL DEFAULT \'00000000000000\' COMMENT \'setvice value,used to remove old data after reimport.\';');
	}

	public function down()
	{
        $this->dropColumn('dialogs', 'import_id');
	}
}