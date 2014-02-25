<?php

class GenerateBankPDFCommand extends CConsoleCommand {

    public function actionIndex($simulations)
    {
        $simulations = explode(',', $simulations);
        foreach($simulations as $sim_id){
            if(!empty($sim_id)){
                $simulation = Simulation::model()->findByPk(trim($sim_id));
                if($simulation !== null){
                    SimulationService::saveAssessmentPDFFilesOnDisk($simulation);
                    echo $sim_id . " - done \r\n";
                }else{
                    echo $sim_id . " - not found \r\n";
                }
            }
        }
    }

}