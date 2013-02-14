<?php

class m130214_162503_rename_dialog_switch_flags extends CDbMigration
{
	public function up()
	{
        $this->renameColumn('dialogs', 'flag', 'flag_to_switch');
	}

	public function down()
	{
        $this->renameColumn('dialogs', 'flag_to_switch', 'flag');
	}
}