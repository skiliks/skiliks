<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TestController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class TestController extends AjaxController{
    
    public function actionTestFinish() {
        $sid = Yii::app()->request->getParam('sid', false);
            SessionHelper::setSid($sid);
        
        $simId = SessionHelper::getSimIdBySid($sid);
        
        $r = SimulationService::calcPoints($simId);
        
    }
}

?>
