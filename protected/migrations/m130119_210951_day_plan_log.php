<?php

class m130119_210951_day_plan_log extends CDbMigration
{
	public function up()
	{
        $this->delete('day_plan_log');
        $this->alterColumn('day_plan_log', 'date', 'time DEFAULT NULL');
        $this->alterColumn('day_plan_log', 'snapshot_date', 'datetime DEFAULT NULL COMMENT \'Дата логирования\'');
	}

	public function down()
	{
        $this->alterColumn('day_plan_log', 'date', 'int(11) DEFAULT NULL');
        $this->alterColumn('day_plan_log', 'snapshot_date', 'int(11) DEFAULT NULL COMMENT \'Дата логирования\'');
	}

}