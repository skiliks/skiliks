<?php

class AuthControllerTest extends ControllerTestCase {
    /**
     * @large
     */
    function testSimulationStart() {
        $user = new Users();
        $user->email = 'andrey' . time() . '@kostenko.name';
        $user->password = md5('test');
        $user->is_active = true;
        $user->save();
        $result = $this->callJSONAction('AuthController', 'actionAuth', array('email' => $user->email, 'pass' => 'test'));
        $sid = $result['sid'];
        unset($result['sid']);
        $this->assertEquals(array(
            'result' => 1,
            'simulations' => array(
                '1' => 'promo'
            )
        ), $result);
        $result = $this->callJSONAction('AuthController', 'actionCheckSession', array('sid' => $sid));
        $this->assertEquals(1, $result['result']);
        $result = $this->callJSONAction('AuthController', 'actionAuth', array('email' => $user->email, 'pass' => 'test1'));
        $this->assertEquals('Неправильное имя пользователя или пароль.', $result['message']);
    }
}
