<?php

class AuthTest extends ControllerTestCase {
    function testSimulationStart() {
        $user = new Users();
        $user->email = 'andrey' . time() . '@kostenko.name';
        $user->password = md5('test');
        $user->is_active = true;
        $user->save();
        $_POST['commandId'] = 2;
        $_POST['email'] = $user->email;
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
