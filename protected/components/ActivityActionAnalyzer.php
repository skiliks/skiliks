<?php
/*
 * @property []UniversalLog $universal_log
 */
class ActivityActionAnalyzer {

    public $simulation;
    public $universal_log;
    public function __construct(Simulation $simulation) {
        $this->simulation = $simulation;
        $this->universal_log = UniversalLog::model()->findAllByAttributes(['sim_id'=>$simulation->id]);

    }

    public function run() {



    }

    public function findActivityActionByLog(UniversalLog $log) {

    }

} 