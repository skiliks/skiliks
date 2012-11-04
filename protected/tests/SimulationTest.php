<?php

class SimulationTest extends ControllerTestCase {
    function testSimulationStart() {
        $session = new UsersSessions();
        $session->user_id = 2;
        $session->session_id = '123';
        $session->save();
        $_POST['sid'] = $session->session_id;
        $_POST['stype'] = 1;
        $result = $this->callJSONAction('SimulationController', 'actionStart');

        $this->assertEquals(array("result" => 1,"speedFactor" => 8), $result);
        $result = $this->callJSONAction('MailController', 'actionGetInboxUnreadedCount');
        $this->assertEquals(array('result' => 1, 'unreaded' => 4), $result);
        $result = $this->callJSONAction('TodoController', 'actionGetCount');
        $this->assertEquals(array('result' => 1, 'data' => 18), $result);
        $result = $this->callJSONAction('EventsController', 'actionGetState');
        $this->assertEquals(array('result' => 0, 'code' => 4, 'serverTime' => 540 /* WTF */, 'message' => 'Нет ближайших событий'), $result);
    }

    function testSimulatorFail() {
        $_POST['sid'] = 'non-existent';
        $result = $this->callJSONAction('SimulationController', 'actionStart');
        $this->assertEquals(array("result" => 0,"message" => "Не могу найти такого пользователя"), $result);
    }
}
