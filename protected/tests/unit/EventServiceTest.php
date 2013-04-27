<?php

class EventServiceTest extends PHPUnit_Framework_TestCase
{

    public function testAddByCode()
    {

        $events = [
            ['code' => 'S1.1', 'time' => '11:05:00', 'standard_time' => '11:10:00'],
            ['code' => 'S1.2', 'time' => '11:10:00', 'standard_time' => '11:20:00']
        ];

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        foreach ($events as $e) {
            EventService::addByCode($e['code'], $simulation, $e['time']);
            $event = $simulation->game_type->getEventSample(['code' => $e['code']]);
            $event = EventTrigger::model()->bySimIdAndEventId($simulation->id, $event->id)->find();
            $this->assertEquals($e['standard_time'], $event->trigger_time);
        }

    }

    /**
     * Проверяет, что письма приходят мгновенно
     */
    public function testImmediateMail()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);
        $eventManager = new EventsManager();
        EventService::addByCode('E1', $simulation, '09:41');
        EventService::addByCode('E9', $simulation, '09:42');
        EventService::addByCode('E8', $simulation, '09:43');
        EventService::addByCode('M2', $simulation, '09:44');
        EventService::addByCode('E2', $simulation, '09:45');
        $stateResult = $eventManager->getState($simulation, []);
        $this->assertEquals($stateResult['events'][0]['eventType'], 'M');
        $stateResult = $eventManager->getState($simulation, []);
        $this->assertEquals($stateResult['events'][0]['data'][0]['code'], 'E1');
        $stateResult = $eventManager->getState($simulation, []);
        $this->assertEquals('E9', $stateResult['events'][0]['data'][0]['code']);
        $stateResult = $eventManager->getState($simulation, []);
        $this->assertEquals('E8', $stateResult['events'][0]['data'][0]['code']);
        $stateResult = $eventManager->getState($simulation, []);
        $this->assertEquals('E2', $stateResult['events'][0]['data'][0]['code']);
    }

    /*
     * Проверка того, что T2 и события типа P нормально приходят
     */
    public function testProcessLinkedEntities()
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {

            $user = YumUser::model()->findByAttributes(['username' => 'asd']);
            $invite = new Invite();
            $invite->scenario = new Scenario();
            $invite->receiverUser = $user;
            $invite->scenario->slug = Scenario::TYPE_FULL;
            $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

            $result = EventService::processLinkedEntities('T', $simulation);

            $this->assertEquals($result, [
                'result' => 1,
                'eventType' => 1
            ]);
            $result = EventService::processLinkedEntities(Replica::model()->findByAttributes(['next_event_code' =>'P5'])->next_event_code, $simulation);
            $this->assertEquals('P', $result['eventType']);
            $this->assertEquals(Task::model()->findByPk($result['id'])->code, 'P5');
            $MS27Replica = Replica::model()->findByAttributes(['next_event_code' => 'MS27']);
            $MS29Replica = Replica::model()->findByAttributes(['next_event_code' => 'MS29']);
            $result = EventService::processLinkedEntities($MS27Replica->next_event_code, $simulation);
            $this->assertEquals('MS', $result['eventType']);
            $result = EventService::processLinkedEntities($MS27Replica->next_event_code, $simulation, true);
            print_r($result);
            $result = EventService::processLinkedEntities($MS29Replica->next_event_code, $simulation, true);
            $this->assertArrayHasKey('fantastic', $result);
            $this->assertEquals(MailTemplate::model()->findByAttributes(['code' => 'MS29', 'scenario_id' => $simulation->scenario_id])->subject_id, $result['mailFields']['subjectId']);
            $this->assertEquals('MS', $result['eventType']);
            $result = EventService::processLinkedEntities('M11', $simulation);
            $this->assertEquals($result['eventType'], 'M');
            $this->assertEquals(MailBox::model()->findByPk($result['id'])->code, 'M11');
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }

    }

    /*
     * Проверка на то что если E1 был запущен, то его уже нет в стеке событий
     */
    public function testEventNotStart()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        $dialog = new DialogService();
        $dialog_cancel = Replica::model()->findByAttributes(['code' => 'S1.1', 'replica_number' => 1]);

        $dialog->getDialog($simulation->id, $dialog_cancel->id, '9:05');
        $dialog_call = Replica::model()->findByAttributes(['code' => 'E1', 'replica_number' => 0]);
        $dialog->getDialog($simulation->id, $dialog_call->id, '9:06');
        $event = EventSample::model()->findByAttributes(['code' => 'E1']);

        $res = EventTrigger::model()->findByAttributes(['event_id' => $event->id, 'sim_id' => $simulation->id]);

        $this->assertEquals(null, $res);
    }

}
