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
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $user = YumUser::model()->findByAttributes(['username' => 'asd']);
            $simulation = SimulationService::simulationStart(1, $user);
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
            $result = EventService::processLinkedEntities($MS29Replica->next_event_code, $simulation, true);
            $this->assertArrayHasKey('fantastic', $result);
            $this->assertEquals(MailTemplate::model()->findByAttributes(['code' => 'MS29'])->subject_id, $result['mailFields']['subject_id']);
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
