<?php

class SimulationTest extends ControllerTestCase
{
    /**
     * @medium
     */
    function testSimulationStart()
    {
        $this->markTestIncomplete();
        $_POST['commandId'] = 2;
        $_POST['email'] = 'asd';
        $_POST['pass']  = '123';
        $result = $this->callJSONAction('AuthController', 'actionAuth');
        $sid = $result['sid'];
        $_POST['sid'] = $result['sid'];

        $simulation = SimulationService::simulationStart(1);
        $result = $this->callJSONAction('MailController', 'actionGetInboxUnreadCount');
        $this->assertEquals(array('result' => 1, 'unreaded' => 4), $result);
        $result = $this->callJSONAction('TodoController', 'actionGetCount');
        $this->assertEquals(array('result' => 1, 'data' => 18), $result);
        $result = $this->callJSONAction('EventsController', 'actionGetState');
        $this->assertEquals(540, round($result['serverTime'], -1), 10);
        unset($result['serverTime']);
        $this->assertEquals(array('result' => 0, 'code' => 4, 'message' => 'Нет ближайших событий'), $result);
        $_POST['hour'] = 10;
        $_POST['minute'] = 0;
        $result = $this->callJSONAction('SimulationController', 'actionChangeTime');
        $this->assertEquals(array('result' => 1), $result);
        $result = $this->callJSONAction('EventsController', 'actionGetState');
        $result['serverTime'] = round($result['serverTime'], -1);
        $this->assertEquals(array('result' => 1,
            'serverTime' => 600,
            'events' => array(
                array(
                    'result' => 1,
                    'eventType' => 1,
                    'data' => array(
                        array(
                            'id' => 620,
                            'ch_from' => 36,
                            'ch_from_state' => 1,
                            'ch_to' => 1,
                            'ch_to_state' => 1,
                            'dialog_subtype' => 1,
                            'text' => 'звук телефонного звонка',
                            'sound' => '',
                            'duration' => 0,
                            'title' => 'Шиномонтаж',
                            'name' => ''
                        ), array(
                            'id' => 621,
                            'ch_from' => 1,
                            'ch_from_state' => 1,
                            'ch_to' => 36,
                            'ch_to_state' => 1,
                            'dialog_subtype' => 1,
                            'text' => 'Ответить',
                            'sound' => '',
                            'duration' => 0
                        ), array(
                            'id' => 622,
                            'ch_from' => 1,
                            'ch_from_state' => 1,
                            'ch_to' => 36,
                            'ch_to_state' => 1,
                            'dialog_subtype' => 1,
                            'text' => 'Не ответить',
                            'sound' => '',
                            'duration' => 0
                        )
                    )
                )
            )
        ), $result);
        $result = $this->callJSONAction('DialogController', 'actionGet');
        $this->assertEquals(array('result' => 1, 'events' => array(array('result' => 1, 'data' => array(), 'eventType' => 1))), $result);
        $result = $this->callJSONAction('EventsController', 'actionGetState');
        $this->callJSONAction('AuthController', 'actionLogout');
    }

    function testChangeTime() {
        $this->markTestIncomplete();

        $_POST['commandId'] = 2;
        $_POST['email'] = 'asd';
        $_POST['pass']  = '123';
        $result = $this->callJSONAction('AuthController', 'actionAuth');
        $_POST['sid'] = $result['sid'];
        $simulation = SimulationService::simulationStart(1);
        SimulationService::setSimulationClockTime($simulation, 13, 30);
        $simulation->deleteOldTriggers(13, 30);
        foreach (EventsTriggers::model()->findAllByAttributes(['sim_id' => $simulation->id]) as $event) {
            if (preg_match('/^M/', $event->event_sample->code)) {
                continue;
            }
            $this->assertTrue($event->trigger_time >= 13*60+30);
        }
    }

    /**
     * @medium
     */
    function testSimulatorFail()
    {
        $this->markTestIncomplete();
        $_POST['sid'] = 'non-existent';
        $result = $this->callJSONAction('SimulationController', 'actionStart');
        $this->assertEquals(0, $result["result"]);
    }
}
