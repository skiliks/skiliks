<?php

class m130715_142203_meeting_window extends CDbMigration
{
	public function up()
	{
        $this->insert('window', [
            'id' => 33,
            'type' => 'visitor',
            'subtype' => 'visitor meeting'
        ]);
	}

	public function down()
	{
        $this->delete('window', 'id = 33');
	}
}