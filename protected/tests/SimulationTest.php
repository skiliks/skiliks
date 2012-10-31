<?php
class SimulationTest extends CDbTestCase {
    function testSimulationStart() {
        Yii::import('application.controllers.*');
        $user = Users::model()->findByPk(1);
        $session = new UsersSessions();
        $session->user_id = 2;
        $session->session_id = '123';
        $session->save();
        $_POST['sid'] = $session->session_id;
        $_POST['stype'] = 1;
        $controller = new SimulationController("simulation");
        ob_start();
        $controller->actionStart();
        $this->assertEquals(ob_get_contents(), '{"result":1,"speedFactor":8}');
        ob_clean();
    }
}
