<?php
class SimulationTest extends CDbTestCase {
    function testSimulationStart() {
        Yii::import('application.controllers.*');
        ob_start();
        $user = Users::model()->findByPk(1);
        $session = new UsersSessions();
        $session->user_id = 2;
        $session->session_id = '123';
        $session->save();
        $_POST['sid'] = $session->session_id;
        $_POST['stype'] = 1;
        $controller = new SimulationController("simulation");
        $controller->actionStart();
        echo ob_get_contents();
    }
}
