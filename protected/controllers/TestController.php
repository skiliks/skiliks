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
    
    public function actionTestCsv() {
        $data = array(
            array('key1'=>'value1', 'key2'=>'value2')
        );
        
        $cmd = Yii::app()->db->createCommand("SELECT * FROM window_log");
 
        $csv = new ECSVExport($cmd, true, true, ';');
        $content = $csv->toCSV(); // returns string by default
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv", false);
        exit();
        //echo($output);
    }
}

?>
