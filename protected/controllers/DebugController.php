<?php

/**
 * Description of DebugController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DebugController extends AjaxController{

    public function actionIndex(){

        echo Dialogs::model()->findByAttributes(['code' => 'ET1.1', 'replica_number'=>2])->id;
    }
    
    /**
     * @deprecated
     */
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
        $simulation = Simulations::model()->findByPk(7277);
        
        echo '<pre>';
        
        echo 'start';
        //$CheckConsolidatedBudget = new CheckConsolidatedBudget($simulation->id);
        //$CheckConsolidatedBudget->calcPoints();
        //LogHelper::fixLogWhenSimStop($simulation);
        $s = new ImportGameDataService();
        $s->importAll();
        echo '<br/>end.';
        
        //$import = new ImportGameDataService();
        //$result = $import->importCharactersPointsTitles();
        //var_dump($result);
        //SimulationService::saveAgregatedPoints(6159);
        //LogHelper::getDialogPointsDetail(LogHelper::RETURN_DATA, array('sim_id' => 5002));
        
        //SimulationService::saveEmailsAnalize(5002);
        //$ea = new EmailAnalizer(6188);
        
        /*$aa = SimulationService::getAgregatedPoints(3938);
        
        foreach ($aa as $line) {
            var_dump($line->getValue());
            $line->mark = null;
            var_dump($line);
        }
        
        SimulationService::saveAgregatedPoints(3938);*/
        //MailBoxService::updateMsCoincidernce(696644, 6188);
        /*
        $a = new EmailCoincidenceAnalizator();
        $a->setUserEmail(696644);
        var_dump($a->checkCoinsidence());
        */

        /*echo "<br/>3322 3324:<br/> <pre>";
        
        $v = $ea->check_3322_3324();
        unset($v['3322']['obj']);
        unset($v['3324']['obj']);
        
        var_dump($v);
        die;*/
        
        /*echo "<br/>3323:<br/> <pre>";
        
        $v = $ea->check_3323();
        unset($v['obj']);
        
        var_dump($v);
        die;*/
        
        /*echo "<br/>Standard:<br/> <pre>";
        
        $v = $ea->standardCheck();
        foreach ($v as $vLine) {
            unset($vLine['obj']);
            var_dump($vLine);
        }
        die;*/
        
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
        echo '</pre>';
    }
}


