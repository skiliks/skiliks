<?php

class m130805_160507_drop_day_plan_tables extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('fk_day_plan_after_vacation_task_id', 'day_plan_after_vacation');
        $this->dropForeignKey('fk_day_plan_after_vacation_sim_id', 'day_plan_after_vacation');

        $this->dropForeignKey('fk_todo_task_id', 'todo');
        $this->dropForeignKey('fk_todo_sim_id', 'todo');
        $this->dropForeignKey('fk_assessment_detail_task_id', 'assessment_points');
        $this->addForeignKey('fk_assessment_detail_task_id', 'assessment_points', 'task_id', 'day_plan', 'id', 'CASCADE', 'CASCADE');

        $this->dropTable('day_plan_after_vacation');
        $this->dropTable('todo');
	}

	public function down()
	{
		$this->createTable('day_plan_after_vacation', [
            'id' => 'pk',
            'task_id' => 'int(11) NOT NULL',
            'sim_id' => 'int(11) NOT NULL',
            'date' => 'time DEFAULT NULL',
        ]);

        $this->addForeignKey('fk_day_plan_after_vacation_task_id', 'day_plan_after_vacation', 'task_id', 'tasks', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_day_plan_after_vacation_sim_id', 'day_plan_after_vacation', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('todo', [
            'id' => 'pk',
            'sim_id' => 'int(11) NOT NULL',
            'task_id' => 'int(11) NOT NULL',
            'adding_date' => 'datetime DEFAULT NULL'
        ]);

        $this->addForeignKey('fk_todo_task_id', 'todo', 'task_id', 'tasks', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_todo_sim_id', 'todo', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');

        $this->dropForeignKey('fk_assessment_detail_task_id', 'assessment_points');
        $this->addForeignKey('fk_assessment_detail_task_id', 'assessment_points', 'task_id', 'todo', 'id', 'CASCADE', 'CASCADE');
	}
}