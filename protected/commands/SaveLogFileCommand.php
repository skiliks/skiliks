<?php
use application\components\Logging\LogTableList as LogTableList;
class SaveLogFileCommand extends CConsoleCommand {

    public function actionIndex($sim_id=null, $overwrite = false, $limit=null)
    {
        $saves = 0;

        if( null === $sim_id ) {
            $scenario = Scenario::model()->findByAttributes(['slug'=>'full']);
            /* @var $simulations []Simulation */
            $simulations = Simulation::model()->findAllByAttributes(['scenario_id'=>$scenario->id]);

            foreach($simulations as $simulation) {

                if(file_exists($simulation->getLogFilename()) === false) {
                    if($limit !== null){
                        if($saves >= (int)$limit) {
                            break;
                        }
                    }
                    echo $simulation->saveLogsAsExcel(true);
                    $saves++;
                } else {
                    if($overwrite) {
                        if($limit !== null){
                            if($saves >= (int)$limit) {
                                break;
                            }
                        }
                        if(unlink($simulation->getLogFilename())){
                            echo $simulation->saveLogsAsExcel(true);
                            $saves++;
                        } else {
                            throw new Exception("file not delete - ".$simulation->getLogFilename());
                        }
                    }else{
                        echo $simulation->getLogFilename()." - already stored \r\n";
                    }
                }
            }
        } else {
            /** @var $simulation Simulation */
            $simulation = Simulation::model()->findByPk($sim_id);
            if(null !== $simulation) {
                if(file_exists($simulation->getLogFilename()) === false) {
                    echo $simulation->saveLogsAsExcel(true);
                } else {
                    if($overwrite) {
                        if(unlink($simulation->getLogFilename())){
                            echo $simulation->saveLogsAsExcel(true);
                            $saves++;
                        } else {
                            throw new Exception("file not delete - ".$simulation->getLogFilename());
                        }
                    } else {
                        throw new Exception("File is exists on ".$simulation->getLogFilename());
                    }
                }
            } else {
                throw new Exception("Simulation not found by id - {$sim_id}");
            }
        }
        echo " {$saves} files stored!\r\n";
    }
}