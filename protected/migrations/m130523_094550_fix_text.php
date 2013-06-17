<?php

class m130523_094550_fix_text extends CDbMigration
{
	public function up()
	{
        $specs = ProfessionalSpecialization::model()->findAllByAttributes(['label'=>"Постродажное обслуживание"]);
        foreach($specs as $spec){
            $spec->label = "Постпродажное обслуживание";
            $spec->save();
            echo "Постпродажное обслуживание \r\n";
        }
	}

	public function down()
	{
		echo "m130523_094550_fix_text does not support migration down.\n";
		return false;
	}

}