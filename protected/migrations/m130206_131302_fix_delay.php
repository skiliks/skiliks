<?php

class m130206_131302_fix_delay extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('dialogs', 'delay', 'INT(11) NOT NULL DEFAULT 0');
        $this->dropColumn('dialogs', 'duration');
        $service = new ImportGameDataService();
        $service->importAll();
	}

	public function down()
	{
		echo "m130206_131302_fix_delay does not support migration down.\n";
		return false;
	}

}