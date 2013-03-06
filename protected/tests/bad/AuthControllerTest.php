<?php

class AuthControllerTest extends ControllerTestCase {
    /**
     * @large
     */
    function testSimulationStart()
    {
        $this->markTestIncomplete();
        $user = new YumUser();
        $user->email = 'andrey' . time() . '@kostenko.name';
        $user->password = md5('test');
        $user->is_active = true;
        $user->save();
        $identity = new BackendUserIdentity($user->email, 'test');
        $identity->authenticate();
        Yii::app()->user->login($identity, 3600 * 12);
        Yii::app()->session['uid'] = Yii::app()->user->id;

        $this->assertEquals(UserService::getModes(Yii::app()->user),["1" => "promo"]);

        $identity = new BackendUserIdentity($user->email, 'test1');
        $this->assertFalse($identity->authenticate());
    }
}
