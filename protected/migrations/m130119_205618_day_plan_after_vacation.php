<?php

class m130119_205618_day_plan_after_vacation extends CDbMigration
{
	public function up()
	{
        $this->delete('day_plan_after_vacation');
        $this->alterColumn('day_plan_after_vacation', 'date', 'time DEFAULT NULL');
	}

	public function down()
	{
        $this->alterColumn('day_plan_after_vacation', 'date', 'int(11) DEFAULT NULL');
	}

}