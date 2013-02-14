<?php

class m130213_133021_phone_calls extends CDbMigration
{
	public function up()
	{
        $this->renameColumn('phone_calls', 'dialog_id', 'dialog_code');
	}

	public function down()
	{
        $this->renameColumn('phone_calls', 'dialog_code', 'dialog_id');
	}

}