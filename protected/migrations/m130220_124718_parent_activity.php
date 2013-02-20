<?php

class m130220_124718_parent_activity extends CDbMigration
{
	public function up()
	{
        $this->createTable('activity_parent', [
            'id'    => 'VARCHAR(10) NOT NULL PRIMARY KEY',
            'state' => 'TINYINT(1) NOT NULL DEFAULT 0',
            'import_id' => 'VARCHAR(14)'
        ]);
	}

	public function down()
	{
		$this->dropTable('activity_parent');
	}
}