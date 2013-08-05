<?php

class m130802_122516_day_plan_refactor extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('day_plan', 'day', 'varchar(50) not null');

        $this->update('day_plan', ['day' => DayPlan::DAY_1], 'day = "1"');
        $this->update('day_plan', ['day' => DayPlan::DAY_2], 'day = "2"');

        $afterVacationRecords = $this->dbConnection->createCommand('SELECT * FROM `day_plan_after_vacation`')->queryAll();
        foreach ($afterVacationRecords as $afterVacationRecord) {
            $this->insert('day_plan', [
                'sim_id' => $afterVacationRecord['sim_id'],
                'date' => $afterVacationRecord['date'],
                'day' => DayPlan::DAY_AFTER_VACATION,
                'task_id' => $afterVacationRecord['task_id']
            ]);
        }

        $this->delete('day_plan_after_vacation');

        $todoRecords = $this->dbConnection->createCommand('SELECT * FROM `todo`')->queryAll();
        foreach ($todoRecords as $todoRecord) {
            $this->insert('day_plan', [
                'sim_id' => $todoRecord['sim_id'],
                'day' => DayPlan::DAY_TODO,
                'task_id' => $todoRecord['task_id']
            ]);
        }

        $this->delete('todo');
	}

	public function down()
	{
        foreach (DayPlan::model()->findAllByAttributes(['day' => DayPlan::DAY_TODO]) as $afterVacationRecord) {
            $this->insert('day_plan_after_vacation', [
                'sim_id' => $afterVacationRecord->sim_id,
                'task_id' => $afterVacationRecord->task_id,
                'date' => $afterVacationRecord->date
            ]);
        }

        foreach (DayPlan::model()->findAllByAttributes(['day' => DayPlan::DAY_AFTER_VACATION]) as $todoRecord) {
            $this->insert('todo', [
                'sim_id' => $todoRecord->sim_id,
                'task_id' => $todoRecord->task_id
            ]);
        }

        $this->delete('day_plan', 'day IN (:afterVacation, :todo)', [
            'afterVacation' => DayPlan::DAY_AFTER_VACATION,
            'todo' => DayPlan::DAY_TODO
        ]);

        $this->update('day_plan', ['day' => '1'], 'day = :day1', ['day1' => DayPlan::DAY_1]);
        $this->update('day_plan', ['day' => '2'], 'day = :day2', ['day2' => DayPlan::DAY_2]);

        $this->alterColumn('day_plan', 'day', 'tinyint(1) not null');
	}
}