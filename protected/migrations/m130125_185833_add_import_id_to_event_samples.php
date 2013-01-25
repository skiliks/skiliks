<?php

class m130125_185833_add_import_id_to_event_samples extends CDbMigration
{
	public function up()
	{
        $this->addColumn('events_samples', 'import_id', 'VARCHAR(14) DEFAULT NULL COMMENT \'setvice value,used to remove old data after reimport.\';');
	}

	public function down()
	{
		$this->dropColumn('events_samples', 'import_id');
	}
}