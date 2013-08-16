<?php

class ActivityActionAnalyzerTest extends CDbTestCase {

    public function testFindActivityActionByLog(){
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        /* @var $activityAction ActivityAction */
        $activityAction = ActivityAction::model()->find("scenario_id = :scenario_id and mail_id is not null", ['scenario_id'=>$simulation->game_type->id]);
        /* @var $mailTemplate MailTemplate */
        /* @var $window Window */
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
    }
}
 