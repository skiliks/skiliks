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
        $simulation = SimulationService::simulationStart(1, $user);

        foreach ($events as $e) {
            EventService::addByCode($e['code'], $simulation, $e['time']);
            $event = EventSample::model()->byCode($e['code'])->find();
            $event = EventTrigger::model()->bySimIdAndEventId($simulation->id, $event->id)->find();
            $this->assertEquals($e['standard_time'], $event->trigger_time);
        }

    }

    /*
     * Проверка того, что T2 и события типа P нормально приходят
     */
    public function testProcessLinkedEntities()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(1, $user);
        $result = EventService::processLinkedEntities('T2', $simulation);
        $this->assertEquals($result, false);
        $result = EventService::processLinkedEntities('P5', $simulation);
        $this->assertEquals($result['eventType'], 'P');
        $this->assertEquals(Task::model()->findByPk($result['id'])->code, 'P5');
        $result = EventService::processLinkedEntities('MS33', $simulation);
        $this->assertEquals($result['eventType'], 'MS');
        $result = EventService::processLinkedEntities('MY1', $simulation);
        $this->assertEquals($result['eventType'], 'MY');
        $this->assertEquals(MailBox::model()->findByPk($result['id'])->code, 'MY1');
        $result = EventService::processLinkedEntities('M10', $simulation);
        $this->assertEquals($result['eventType'], 'M');
        $this->assertEquals(MailBox::model()->findByPk($result['id'])->code, 'M10');

    }

    /*
     * Проверка на то что если E1 был запущен, то его уже нет в стеке событий
     */
    public function testEventNotStart()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(1, $user);

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
