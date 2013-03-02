<?php

class m130301_205821_add_dialogs extends CDbMigration
{
	public function up()
	{
        $this->createTable('dialogs', [
            'code'           => 'VARCHAR(10) NOT NULL',
            'title'          => 'VARCHAR(250) NOT NULL',
            'type'           => 'VARCHAR(30)',
            'start_by'       => 'VARCHAR(30)',
            'start_time'     => 'TIME',
            'delay'          => 'INTEGER',
            'category'       => 'INTEGER',
            'is_use_in_demo' => 'TINYINT(1)',
            'import_id' => 'VARCHAR(60) NOT NULL'
        ]);
	}

	public function down()
	{
		$this->dropTable('dialogs');
	}

}