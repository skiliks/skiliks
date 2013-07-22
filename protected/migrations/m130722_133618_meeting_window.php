<?php

class m130722_133618_meeting_window extends CDbMigration
{
	public function up()
	{
        $this->update('window', ['subtype' => 'meeting choice'], 'id = 33');
        $this->insert('window', ['id' => 34, 'type' => 'visitor', 'subtype' => 'meeting gone']);
	}

	public function down()
	{
		$this->delete('window', 'id = 34');
        $this->update('window', ['subtype' => 'visitor meeting'], 'id = 33');
	}
}