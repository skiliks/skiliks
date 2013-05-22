<?php

class m130522_080753_manual_window extends CDbMigration
{
	public function up()
	{
        $this->insert('window', [
            'id' => 2,
            'type' => 'main screen',
            'subtype' => 'manual'
        ]);
	}

	public function down()
	{
		$this->delete('window', 'id = 2');
	}
}