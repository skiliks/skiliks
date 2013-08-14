<?php

class m130715_104043_meeting extends CDbMigration
{
	public function up()
	{
        $this->createTable('meeting', [
            'id' => 'pk',
            'code' => 'varchar(10) not null',
            'name' => 'varchar(100) null',
            'label' => 'text',
            'duration' => 'int default 0',
            'task_id' => 'int',
            'import_id' => 'varchar(14) not null',
            'scenario_id' => 'int not null'
        ]);

        $this->addForeignKey('fk_meeting_task_id', 'meeting', 'task_id', 'tasks', 'id', 'SET NULL', 'CASCADE');
	}

	public function down()
	{
		$this->dropForeignKey('fk_meeting_task_id', 'meeting');
        $this->dropTable('meeting');
	}
}