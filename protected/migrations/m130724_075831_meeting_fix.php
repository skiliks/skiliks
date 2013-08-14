<?php

class m130724_075831_meeting_fix extends CDbMigration
{
	public function up()
	{
        $this->renameColumn('meeting', 'label', 'popup_text');
        $this->addColumn('meeting', 'icon_text', 'varchar(100) default null');
	}

	public function down()
	{
		$this->renameColumn('meeting', 'popup_text', 'label');
        $this->dropColumn('meeting', 'icon_text');
	}
}