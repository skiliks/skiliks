<?php

class SimulationTest extends ControllerTestCase
{
    function testSimulationStart()
    {
        $session = new UsersSessions();
        $session->user_id = 2;
        $session->session_id = '123';
        $session->save();
        $_POST['sid'] = $session->session_id;
        $_POST['stype'] = 1;
        $result = $this->callJSONAction('SimulationController', 'actionStart');

        $this->assertEquals(array("result" => 1, "speedFactor" => 8), $result);
        $result = $this->callJSONAction('MailController', 'actionGetInboxUnreadedCount');
        $this->assertEquals(array('result' => 1, 'unreaded' => 4), $result);
        $result = $this->callJSONAction('TodoController', 'actionGetCount');
        $this->assertEquals(array('result' => 1, 'data' => 18), $result);
        $result = $this->callJSONAction('EventsController', 'actionGetState');
        $this->assertEquals(array('result' => 0, 'code' => 4, 'serverTime' => 540, 'message' => 'Нет ближайших событий'), $result);
        $_POST['hour'] = 10;
        $_POST['minute'] = 0;
        $result = $this->callJSONAction('SimulationController', 'actionChangeTime');
        $this->assertEquals(array('result' => 1), $result);
        $result = $this->callJSONAction('EventsController', 'actionGetState');
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
        print_r($result);
    }

    function testSimulatorFail()
    {
        $_POST['sid'] = 'non-existent';
        $result = $this->callJSONAction('SimulationController', 'actionStart');
        $this->assertEquals(array("result" => 0, "message" => "Не могу найти такого пользователя"), $result);
    }
}
