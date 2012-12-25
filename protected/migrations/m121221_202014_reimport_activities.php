<?php

class m121221_202014_reimport_activities extends CDbMigration
{
	public function up()
	{
        $import = new ImportGameDataService();
        $result1 = $import->importActivity();
        var_dump($result1);
        
        $result2 = $import->importActivityEfficiencyConditions();
        var_dump($result2);
	}

	public function down()
	{

	}
}