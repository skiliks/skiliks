<?php

class AuthTest extends ControllerTestCase {
    function testSimulationStart() {
        $_POST['commandId'] = 2;
        $_POST['email'] = 'andrey1@kostenko.name';
        $_POST['pass']  = 'test';
        $result = $this->callJSONAction('AuthController', 'actionAuth');
        $sid = $result['sid'];
        unset($result['sid']);
        $this->assertEquals(array(
            'result' => 1,
            'simulations' => array(
                '1' => 'promo'
            )
        ), $result);
    }
}
