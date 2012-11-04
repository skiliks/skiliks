<?php
/**
 * User: gugu
 * Date: 03.11.12
 * Time: 13:23
 */
class ControllerTestCase extends CDbTestCase
{
    protected function setUp()
    {
        Yii::import('application.controllers.*');
        parent::setUp();
    }

    protected function callAction($controller_class, $action) {
        $controller = new $controller_class($controller_class);
        $controller->is_test = true;
        ob_start();
        $controller->$action();
        $result = ob_get_contents();
        ob_clean();
        return $result;
    }

    protected  function callJSONAction($controller_class, $action)
    {
        return CJSON::decode($this->callAction($controller_class, $action));
    }
}
