<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DebugController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DebugController extends AjaxController{
    
    public function actionCalcExcel() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            SessionHelper::setSid($sid);
        
            $simId = SessionHelper::getSimIdBySid($sid);
            
            $result = array('result' => (int)SimulationService::calcPoints($simId));
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array('result' => 0, 'message' => $exc->getMessage());
            return $this->_sendResponse(200, CJSON::encode($result));
        }    
    }
}

?>
