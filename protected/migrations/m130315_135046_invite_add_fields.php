<?php

class m130315_135046_invite_add_fields extends CDbMigration
{
	public function up()
	{
        $this->addColumn('invites', 'position_id', 'int');
        $this->addColumn('invites', 'status', 'TINYINT NOT NULL DEFAULT 0');
        $this->addColumn('invites', 'sent_time', 'timestamp');
	}

	public function down()
	{
        $this->dropColumn('invites', 'position_id');
        $this->dropColumn('invites', 'status');
        $this->dropColumn('invites', 'sent_time');
	}
}