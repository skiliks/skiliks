<?php

class m131220_145447_rename_phone_number extends CDbMigration
{
	public function up()
	{
        $this->renameColumn('outgoing_phone_themes', 'phone_dialog_number', 'dialog_code');
	}

	public function down()
	{
        $this->renameColumn('outgoing_phone_themes', 'dialog_code', 'phone_dialog_number');
	}
}