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
}