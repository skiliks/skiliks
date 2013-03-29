<?php

class m130329_114528_update_tasks_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn('tasks', 'time_limit_type', 'VARCHAR(30) DEFAULT NULL');
        $this->addColumn('tasks', 'fixed_day', 'VARCHAR(30) DEFAULT NULL');
        $this->addColumn('tasks', 'is_cant_be_moved', 'TINYINT(1) DEFAULT 0');

        $this->dropColumn('tasks', 'type');
	}

	public function down()
	{
        $this->dropColumn('tasks', 'time_limit_type');
        $this->dropColumn('tasks', 'fixed_day');
        $this->dropColumn('tasks', 'is_cant_be_moved');

        $this->addColumn('tasks', 'type', 'INT(11)');
	}
}