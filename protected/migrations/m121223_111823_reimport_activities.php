<?php

class m121223_111823_reimport_activities extends CDbMigration
{
	public function up()
	{
        $import = new ImportGameDataService();
        $result2 = $import->importActivityEfficiencyConditions();
        var_dump($result2);
	}

	public function down()
	{

	}
}