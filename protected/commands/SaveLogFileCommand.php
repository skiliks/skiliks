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

                if(file_exists($this->getFilename($simulation->id)) === false) {
                    if($limit !== null){
                        if($saves >= (int)$limit) {
                            break;
                        }
                    }
                    $this->saveFile($simulation);
                    $saves++;
                } else {
                    if($overwrite) {
                        if($limit !== null){
                            if($saves >= (int)$limit) {
                                break;
                            }
                        }
                        if(unlink($this->getFilename($simulation->id))){
                            $this->saveFile($simulation);
                            $saves++;
                        } else {
                            throw new Exception("file not delete - ".$this->getFilename($simulation->id));
                        }
                    }else{
                        echo $this->getFilename($simulation->id)." - already stored \r\n";
                    }
                }
            }
        } else {
            /** @var $simulation Simulation */
            $simulation = Simulation::model()->findByPk($sim_id);
            if(null !== $simulation) {
                if(file_exists($this->getFilename($simulation->id)) === false) {
                    $this->saveFile($simulation);
                } else {
                    if($overwrite) {
                        if(unlink($this->getFilename($simulation->id))){
                            $this->saveFile($simulation);
                            $saves++;
                        } else {
                            throw new Exception("file not delete - ".$this->getFilename($simulation->id));
                        }
                    } else {
                        throw new Exception("File is exists on ".$this->getFilename($simulation->id));
                    }
                }
            } else {
                throw new Exception("Simulation not found by id - {$sim_id}");
            }
        }
        echo " {$saves} files stored!\r\n";
    }

    private function getFilename($sim_id) {
        return __DIR__.'/../logs/'.sprintf("%s-log.xlsx", $sim_id);
    }

    private function saveFile(Simulation $simulation) {
        $logTableList = new LogTableList($simulation);
        $excelWriter = $logTableList->asExcel();
        $excelWriter->save($this->getFilename($simulation->id));
        echo $this->getFilename($simulation->id)."- stored \r\n";
        return true;
    }

}