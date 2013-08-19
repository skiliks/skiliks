<?php

class DayPlanAndTodoUnitTest extends CDbTestCase
{
    public function testGetTodoTasksList()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $gotRecords = DayPlanService::getTodoList($simulation);
        $inDBRecords = DayPlan::model()->findAllByAttributes([
            'sim_id' => $simulation->id,
            'day' => DayPlan::DAY_TODO
        ]);

        $this->assertEquals(count($gotRecords), count($inDBRecords));
    }

    public function testAddTask()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // Try add new task
        $newTask = $simulation->game_type->getTask(['code' => 'P5']);
        $result = DayPlanService::addTask($simulation, $newTask->id, DayPlan::DAY_TODO);
        $todo = DayPlan::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'task_id' => $newTask->id
        ]);

        $this->assertTrue($result);
        $this->assertInstanceOf('DayPlan', $todo);

        // Try add same task again
        DayPlanService::addTask($simulation, $newTask->id, DayPlan::DAY_TODO);
        $again = DayPlan::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'task_id' => $newTask->id
        ]);

        $this->assertInstanceOf('DayPlan', $again);
        $this->assertSame($todo->id, $again->id);

        // Something weird
        $result = DayPlanService::addTask($simulation, null, null);
        $this->assertSame(false, $result);
    }

    public function testDayPlanSave()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // Try add new task
        $newTask = $simulation->game_type->getTask(['code' => 'P5']);
        $result = DayPlanService::addTask($simulation, $newTask->id, DayPlan::DAY_1, '11:00:00');
        $todo = DayPlan::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'task_id' => $newTask->id
        ]);

        $this->assertTrue($result);
        $this->assertInstanceOf('DayPlan', $todo);

        $result = DayPlanService::saveToXLS($simulation);
        $documentId = $result['docId'];

        $dayPlanExcelDocument = MyDocument::model()->findByPk($documentId);

        $excelSheetList = $dayPlanExcelDocument->getSheetList();

        $this->assertNotNull($excelSheetList[0]['content']);
        $this->assertTrue(strpos($excelSheetList[0]['content'], $newTask->title) > 0);
    }
}