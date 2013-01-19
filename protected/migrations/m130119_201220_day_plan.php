<?php

class m130119_201220_day_plan extends CDbMigration
{
	public function up()
	{
        $this->delete('day_plan');
        $this->alterColumn('day_plan', 'date', 'time DEFAULT NULL');
	}

	public function down()
	{
        $this->alterColumn('day_plan', 'date', 'int(11) DEFAULT NULL');
	}

}