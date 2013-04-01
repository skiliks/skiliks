<?php

class m130401_105717_1april extends CDbMigration
{
	public function up()
	{
        $this->insert('window', ['id' => 51, 'type' => 'browser', 'subtype' => 'browser main']);
	}

	public function down()
	{
        $this->delete('window', " type = 'browser' ");
	}
}