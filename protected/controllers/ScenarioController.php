<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ScenarioController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ScenarioController extends AjaxController{
    
    public function actionUpload() {
        Logger::debug('i was called');
        return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'message' => 'i was called')));
    }
}

?>
