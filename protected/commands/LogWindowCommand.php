<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 24.04.13
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */

class LogWindowCommand extends CConsoleCommand {
    public function actionIndex($sim_id){
        /** @var Simulation $simulation */
        $simulation = Simulation::model()->findByPk($sim_id);
        foreach ($simulation->log_windows as $log) {
            echo '$lw = new LogWindow();' . "\n";
            echo '$lw->sim_id = $sim_id;'. "\n";
            echo '$lw->window = "' . $log->window . '";'. "\n";
            echo '$lw->start_time = "' . $log->start_time . '";'. "\n";
            echo '$lw->end_time = "' . $log->end_time . '";'. "\n";
            echo '$lw->window_uid = "' . $log->window_uid . '";'. "\n";
            echo '$lw->save();' . "\n";
        }
    }
}