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
            
            $CheckConsolidatedBudget = new CheckConsolidatedBudget($simId);
            
            $result = array('result' => (int)$CheckConsolidatedBudget->calcPoints());
            return $this->sendJSON($result);
        } catch (Exception $exc) {
            $result = array('result' => 0, 'message' => $exc->getMessage());
            return $this->sendJSON($result);
        }    
    }
    
    public function actionAe()
    {
        //SimulationService::saveEmailsAnalize(2962);
        $ea = new EmailAnalizer(3894);

        die();
        
        /*echo "<br/>3322 3324:<br/> <pre>";
        
        $v = $ea->check_3322_3324();
        unset($v['3322']['obj']);
        unset($v['3324']['obj']);
        
        var_dump($v);*/
        
        
        /*echo "<br/>Big tasks emails:<br/>";
        var_dump($ea->checkBigTasks());
        
        echo "<br/>Small task email:s<br/>";
        var_dump($ea->checkSmallTasks());
        */
        /*echo "<br/>Spams emails:<br/>";
        $v = $ea->check_3325();
        unset($v['obj']);
        var_dump($v);
        echo "</pre>";*/
    }
}


