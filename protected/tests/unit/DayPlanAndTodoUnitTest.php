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

        $gotRecords = TodoService::getTodoTasksList($simulation->id);
        $inDBRecords = Todo::model()->findAllByAttributes([
            'sim_id' => $simulation->id
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
        $result = TodoService::addTask($newTask->id, $simulation->id);
        $todo = Todo::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'task_id' => $newTask->id
        ]);

        $this->assertTrue($result);
        $this->assertInstanceOf('Todo', $todo);

        // Try add same task again
        $result = TodoService::addTask($newTask->id, $simulation->id);
        $this->assertInstanceOf('Todo', $result);
        $this->assertSame($todo->id, $result->id);

        // Something weird
        $result = TodoService::addTask(null, $simulation->id);
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