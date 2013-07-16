<?php

class m130716_084449_activity_meeting extends CDbMigration
{
	public function up()
	{
        $this->addColumn('activity_action', 'meeting_id', 'int default null');
        $this->addForeignKey('fk_activity_action_meeting_id', 'activity_action', 'meeting_id', 'meeting', 'id', 'SET NULL', 'CASCADE');

        $this->insert('activity_type', ['type' => 'Meeting']);
	}

	public function down()
	{
        $this->dropForeignKey('fk_activity_action_meeting_id', 'activity_action');
        $this->dropColumn('activity_action', 'meeting_id');

        $this->delete('activity_type', 'type = "Meeting"');
	}
}