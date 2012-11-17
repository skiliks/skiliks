<?php

class LogTest extends ControllerTestCase
{
    function testLogging()
    {
        $_POST['commandId'] = 2;
        $_POST['email'] = 'asd';
        $_POST['pass']  = '123';
        $result = $this->callJSONAction('AuthController', 'actionAuth');
        $_POST['sid'] = $result['sid'];
        $_POST['stype'] = 1;
        $result = $this->callJSONAction('SimulationController', 'actionStart');

        $this->assertEquals(array("result" => 1, "speedFactor" => 8), $result);
        $_POST['logs'] = array(10, 11, 0, 33100, array('mailId'=> 188985));
        $result = $this->callJSONAction('EventsController', 'actionGetState');
        print_r($result);

    }

}
