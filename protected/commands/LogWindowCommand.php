<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 24.04.13
 * Time: 19:25
 * To change this template use File | Settings | File Templates.
 */
/**
 * Что она делает?
 */
class LogWindowCommand extends CConsoleCommand {
    public function actionIndex($sim_id){
        /** @var Simulation $simulation */
        $simulation = Simulation::model()->findByPk($sim_id);
        $data = [];
        foreach ($simulation->log_windows as $log) {
            if (!isset($data[$log->start_time])) {
                $data[$log->start_time] = '';
            }
            $data[$log->start_time] .=
                '$lw = new LogWindow();' . "\n" .
                '$lw->sim_id = $sim_id;'. "\n" .
                '$lw->window = "' . $log->window . '";'. "\n" .
                '$lw->start_time = "' . $log->start_time . '";'. "\n" .
                '$lw->window_uid = "' . $log->window_uid . '";'. "\n" .
                '$lw->save();' . "\n";
            $data[$log->end_time] =
                '$lw = LogWindow::model()->findByAttributes(["end_time" => "00:00:00", "sim_id" => $sim_id, "window_uid" => ' . $log->window_uid . ']);' . "\n" .
                '$lw->end_time = "' . $log->end_time . '";'. "\n" .
                '$lw->save();' . "\n";
        }
        foreach ($simulation->log_mail as $log) {
            if (!isset($data[$log->start_time])) {
                $data[$log->start_time] = '';
            }
            $data[$log->start_time] .=
                '$lw = new LogMail();' . "\n" .
                    '$lw->sim_id = $sim_id;'. "\n" .
                    '$lw->window = "' . $log->window . '";'. "\n" .
                    '$lw->start_time = "' . $log->start_time . '";'. "\n" .
                    '$lw->window_uid = "' . $log->window_uid . '";'. "\n" .
                    '$lw->mail_id = "' . $log->mail_id . '";'. "\n" .
                    '$lw->save();' . "\n";
            echo $log->getPrimaryKey() . "\n";
            $data[$log->end_time] .=
                '$lw = LogMail::model()->findByAttributes(["end_time" => "00:00:00", "sim_id" => $sim_id, "window_uid" => ' . $log->window_uid . ']);' . "\n" .
                    '$lw->end_time = "' . $log->end_time . '";'. "\n" .
                    '$lw->save();' . "\n";
        }
        foreach ($data as $time => $v) {
            echo $v;
        }
    }
}