<?php

class AuthControllerTest extends ControllerTestCase {
    /**
     * @large
     */
    function testSimulationStart() {
        $this->markTestIncomplete();
        $user = new Users();
        $user->email = 'andrey' . time() . '@kostenko.name';
        $user->password = md5('test');
        $user->is_active = true;
        $user->save();
        $identity = new BackendUserIdentity($user->email, 'test');
        $identity->authenticate();
        Yii::app()->user->login($identity, 3600 * 12);
        $sid = Yii::app()->session->sessionID;
        Yii::app()->session['uid'] = Yii::app()->user->id;
        $this->assertEquals(UserService::getGroups(Yii::app()->user->id),["1" => "promo"]);
        $identity = new BackendUserIdentity($user->email, 'test1');
        $this->assertFalse($identity->authenticate());
    }
}
