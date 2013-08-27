<?php

class ActivityActionAnalyzerTest extends CDbTestCase {

    /*public function testFindActivityActionByLog(){
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        $activityAction = ActivityAction::model()->find("scenario_id = :scenario_id and mail_id is not null", ['scenario_id'=>$simulation->game_type->id]);

        $mailTemplate = MailTemplate::model()->findByPk($activityAction->mail_id);
        if($mailTemplate->isMS()){
            $window = Window::model()->findByAttributes(['subtype'=>'mail new']);
            $mail = MailBoxService::copyMessageFromTemplateByCode($simulation, $mailTemplate->code);
        }else{
            $window = Window::model()->findByAttributes(['subtype'=>'mail main']);
            $mail = LibSendMs::sendMsByCode($simulation, $mailTemplate->code);
        }

        $log = new UniversalLog();
        $log->sim_id = $simulation->id;
        $log->mail_id = $mail->id;
        $log->start_time = '10:00:00';
        $log->end_time = '10:30:00';
        $log->window_id = $window->id;
        $log->save();

        $analyzer = new ActivityActionAnalyzer($simulation);
        $analyzer->run();
    }*/

    public function testDebug(){
        $simulation = Simulation::model()->findByPk('958');
        /*$mail_logs = LogMail::model()->findAllByAttributes(['sim_id'=>$simulation->id]);
        foreach($mail_logs as $mail_log){
            $universal_log = UniversalLog::model()->findByAttributes(['sim_id'=>$simulation->id, 'start_time'=>$mail_log->start_time, 'end_time'=>$mail_log->end_time]);
            if(null !== $universal_log){
                $universal_log->window_uid = $mail_log->window_uid;
                $universal_log->update();
            }
        }
        */
        LogActivityAction::model()->deleteAllByAttributes(['sim_id'=>$simulation->id]);
        LogHelper::updateUniversalLog($simulation);
        $analyzer = new ActivityActionAnalyzer($simulation);
        $analyzer->run();
        /*$old_log = LogActivityAction::model()->findAllByAttributes(['sim_id'=>$simulation->id]);
        $new_log = LogActivityActionTest::model()->findAllByAttributes(['sim_id'=>$simulation->id]);
        foreach(Activity::model()->findAll() as $activity){
            $activities[$activity->id] = $activity;
        }
        foreach(ActivityAction::model()->findAll() as $action){
            $actions[$action->id] = $action->activity_id;
        }
        foreach($old_log as $key => $log){
            var_dump('Old activity code '.$activities[$actions[$log->activity_action_id]]->code);
            var_dump('New activity code '.$activities[$actions[$new_log[$key]->activity_action_id]]->code);
            var_dump('Old start time '.$log->start_time);
            var_dump('New start time '.$new_log[$key]->start_time);
            var_dump('Old end time '.$log->end_time);
            var_dump('New end time '.$new_log[$key]->end_time);
            $this->assertEquals($activities[$actions[$log->activity_action_id]]->code, $activities[$actions[$new_log[$key]->activity_action_id]]->code, ' row '.($key));
            $this->assertEquals($log->start_time, $new_log[$key]->start_time, 'start row '.($key));
            $this->assertEquals($log->end_time, $new_log[$key]->end_time, 'end row '.($key));
        }
        /*$logs = LogServerRequest::model()->findAllByAttributes(['sim_id'=>$simulation->id, 'request_url'=>'/index.php/events/getState']);
        UniversalLogTest::model()->deleteAll();
        foreach($logs as $log){
            $request = json_decode($log->request_body, true);
            if(!empty($request['logs'])){
                LogHelper::setUniversalLog($simulation, $request['logs']);
            }
        }
        $logs = LogServerRequest::model()->findAllByAttributes(['sim_id'=>$simulation->id, 'request_url'=>'/index.php/simulation/stop']);
        foreach($logs as $log){
            $request = json_decode($log->request_body, true);
            if(!empty($request['logs'])){
                LogHelper::setUniversalLog($simulation, $request['logs']);
            }
        }*/
    }
}
 