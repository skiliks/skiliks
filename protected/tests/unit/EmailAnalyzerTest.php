<?php

/**
 *
 * @author slavka
 */
class EmailAnalyzerTest extends CDbTestCase
{
    /**
     * Тест оценки 3313:
     * - все письма прочтены
     */
    public function test_3313_ReadAllEmailsCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        // move all not received emails to inbox
        $emailTemplates = MailTemplate::model()->findAll(
            " code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }
        // move all not received emails to inbox }
        
        // mark all inbox emails as read
        MailBox::model()->updateAll([
                'readed' => 1
            ], 
            'sim_id = :sim_id AND group_id = :group_id', 
            [
                'sim_id'   => $simulation->id,
                'group_id' => 1
            ]
        );
        
        $point = HeroBehaviour::model()->findByAttributes([
            'code' => '3313'
        ]);
        
        SimulationService::saveEmailsAnalyze($simulation->id);
        
        $result = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);
        
        $this->assertEquals($result->value, 1);
    }

    /**
     * Тест оценки 3313:
     * - 4 начальных письма прочтены
     */
    public function test_3313_ReadAllInitialEmailsCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        // move all not received emails to inbox
        $emailTemplates = MailTemplate::model()->findAll(
            " code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }
        // move all not received emails to inbox }

        $point = HeroBehaviour::model()->findByAttributes([
            'code' => '3313'
        ]);
        
        SimulationService::saveEmailsAnalyze($simulation->id);
        
        $result = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);
        
        $this->assertEquals($result->value, 0);
    }

    /**
     * Тест оценки 3322/3324:
     */
    public function test_3322_3324_OneFromOneEmailsPlannedRigthCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        
        $point_3322 = HeroBehaviour::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = HeroBehaviour::model()->findByAttributes([
            'code' => '3324'
        ]);

        $email = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M3');
        $email->readed = 1;
        $email->save();
        $email->refresh();
        $emailTask = MailTask::model()->findByAttributes(['code' => 'M3', 'wr' => 'R']);
        MailBoxService::addMailTaskToPlanner($simulation, $email, $emailTask);
        
        $mailPlanWindow = Window::model()->find('subtype = \'mail plan\'');
        
        // add log
        $log = new LogMail();
        $log->sim_id       = $simulation->id;
        $log->mail_id      = $email->id;
        $log->window       = $mailPlanWindow->id;
        $log->mail_task_id = $emailTask->id;
        $log->start_time   = '09:05:00';
        $log->end_time     = '09:06:00';
        $log->save();
        
        SimulationService::saveEmailsAnalyze($simulation->id);
        
        $result_3322 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3324->id,
        ]);
        
        $this->assertEquals($result_3322->value, $point_3322->scale, '3322 1');
        $this->assertEquals($result_3324->value, 0, '3324 1');
    }

    /**
     * Тест оценки 3322/3324:
     */
    public function test_3322_3324_OneFromOneEmailsNotPlannedRigthCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        
        $point_3322 = HeroBehaviour::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = HeroBehaviour::model()->findByAttributes([
            'code' => '3324'
        ]);

        // move all not received emails to inbox
        $emailTemplates = MailTemplate::model()->findAll(
            " code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }
        // move all not received emails to inbox }
        
        $email = MailBox::model()->findByAttributes(['code' => 'M3', 'sim_id' => $simulation->id]);
        $emailTask = MailTask::model()->findByAttributes(['code' => 'M3', 'wr' => 'R']);
        
        SimulationService::saveEmailsAnalyze($simulation->id);
        
        $result_3322 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3324->id,
        ]);
        
        $this->assertEquals($result_3322->value, 0, '3322 3');
        $this->assertEquals($result_3324->value, 0, '3324 3');
    }

    /**
     * Тест оценки 3322/3324:
     */
    public function test_3322_3324_OneFromOneEmailsPlannedWrongCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        
        $point_3322 = HeroBehaviour::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = HeroBehaviour::model()->findByAttributes([
            'code' => '3324'
        ]);

        // move all not received emails to inbox
        /*$emailTemplates = MailTemplate::model()->findAll(
            " code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }*/
        // move all not received emails to inbox }
        
        $email = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M3');
        $email->readed = 1;
        $email->save();
        $emailTask = MailTask::model()->findByAttributes(['code' => 'M3', 'wr' => 'W']);
        $plannerTask = MailBoxService::addMailTaskToPlanner($simulation, $email, $emailTask);
        
        $mailPlanWindow = Window::model()->find('subtype = \'mail plan\'');
        
        // add log
        $log = new LogMail();
        $log->sim_id       = $simulation->id;
        $log->mail_id      = $email->id;
        $log->window       = $mailPlanWindow->id;
        $log->mail_task_id = $emailTask->id;
        $log->start_time   = '09:05:00';
        $log->end_time     = '09:06:00';
        $log->save();
        
        SimulationService::saveEmailsAnalyze($simulation->id);
        
        $result_3322 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3324->id,
        ]);
        
        $this->assertEquals($result_3322->value, 0, '3322 1');
        $this->assertEquals($result_3324->value, $point_3324->scale, '3324 2');
    }

    /**
     * Тест оценки 3322/3324:
     */
    public function test_3322_3324_OneFromOneEmailsNotPlannedWrongCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        
        $point_3322 = HeroBehaviour::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = HeroBehaviour::model()->findByAttributes([
            'code' => '3324'
        ]);

        // move all not received emails to inbox
        $emailTemplates = MailTemplate::model()->findAll(
            " code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }
        // move all not received emails to inbox }
        
        $email = MailBox::model()->findByAttributes(['code' => 'M3', 'sim_id' => $simulation->id]);
        $emailTask = MailTask::model()->findByAttributes(['code' => 'M3', 'wr' => 'W']);
        
        SimulationService::saveEmailsAnalyze($simulation->id);
        
        $result_3322 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3324->id,
        ]);
        
        $this->assertEquals($result_3322->value, 0, '3322 4');
        $this->assertEquals($result_3324->value, 0, '3324 4');
    }
    
    /**
     * Тест оценки 3313:
     * - кейс "нет прочитанных писем"
     */
    public function test_3313_NoReadEmailsCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        
        $point = HeroBehaviour::model()->findByAttributes([
            'code' => '3313'
        ]);

        $mails = MailBox::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        foreach ($mails as $mail) {
            $mail->readed = 0;
            $mail->save();
        }
        
        SimulationService::saveEmailsAnalyze($simulation->id);
        
        $result = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);
        
        $this->assertEquals($result->value, 0);
    }

    /*
     * в ответ на письма можно что-то предпринять в период менее 2 мин
     * (ответное письмо или другое действие)
     * сразу после прочтения соответствующего письма
     */
    public function test_3323_reply_2min()
    {
        //good M47
        $code_3323 = HeroBehaviour::model()->findByAttributes(['code'=>3323]);

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        EventsManager::getState($simulation, [[1, 1, 'activated', 35104, 'window_uid'=>20]]);
        EventsManager::startEvent($simulation, 'M47');
        $mail_event = EventsManager::getState($simulation, []);
        EventsManager::getState($simulation, [
            [1, 1, 'deactivated', 35120, 'window_uid'=>20],
            [10, 11, 'activated', 35120, ['mailId' => $mail_event['events'][0]['id']], 'window_uid'=>30],
        ]);
        MailBoxService::markReaded($mail_event['events'][0]['id']);
        EventsManager::getState($simulation, [
            [10, 11, 'deactivated', 35180, ['mailId' => $mail_event['events'][0]['id']], 'window_uid'=>30],
            [1, 1, 'activated', 35180, 'window_uid'=>20]
        ]);
        //LibSendMs::sendMsByCode($simulation, 'MS63', 35240, 10, 11, 20);
        LibSendMs::sendMsByCodeWithParent($simulation, 'MS46', 35240, 10, 11, 20, $mail_event['events'][0]['id']);
        SimulationService::saveEmailsAnalyze($simulation->id);
        $point = AssessmentCalculation::model()->findByAttributes(['point_id'=>$code_3323->id, 'sim_id'=>$simulation->id]);
        $this->assertEquals('2', $point->value);

        //good M71
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        EventsManager::getState($simulation, [[1, 1, 'activated', 35104, 'window_uid'=>20]]);
        EventsManager::startEvent($simulation, 'M71');
        $mail_event = EventsManager::getState($simulation, []);
        EventsManager::getState($simulation, [
            [1, 1, 'deactivated', 35120, 'window_uid'=>20],
            [10, 11, 'activated', 35120, ['mailId' => $mail_event['events'][0]['id']], 'window_uid'=>30],
        ]);
        MailBoxService::markReaded($mail_event['events'][0]['id']);
        EventsManager::getState($simulation, [
            [10, 11, 'deactivated', 35180, ['mailId' => $mail_event['events'][0]['id']], 'window_uid'=>30],
            [1, 1, 'activated', 35180, 'window_uid'=>20]
        ]);
        //LibSendMs::sendMsByCode($simulation, 'MS63', 35240, 10, 11, 20);
        LibSendMs::sendMsByCodeWithParent($simulation, 'MS63', 35240, 10, 11, 20, $mail_event['events'][0]['id']);
        SimulationService::saveEmailsAnalyze($simulation->id);
        $point = AssessmentCalculation::model()->findByAttributes(['point_id'=>$code_3323->id, 'sim_id'=>$simulation->id]);
        $this->assertEquals('2', $point->value);

        //good M47 and M71

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        EventsManager::getState($simulation, [[1, 1, 'activated', 35104, 'window_uid'=>20]]);
        EventsManager::startEvent($simulation, 'M47');
        $mail_event = EventsManager::getState($simulation, []);
        EventsManager::getState($simulation, [
            [1, 1, 'deactivated', 35120, 'window_uid'=>20],
            [10, 11, 'activated', 35120, ['mailId' => $mail_event['events'][0]['id']], 'window_uid'=>30],
        ]);
        MailBoxService::markReaded($mail_event['events'][0]['id']);
        EventsManager::getState($simulation, [
            [10, 11, 'deactivated', 35180, ['mailId' => $mail_event['events'][0]['id']], 'window_uid'=>30],
            [1, 1, 'activated', 35180, 'window_uid'=>20]
        ]);
        //LibSendMs::sendMsByCode($simulation, 'MS63', 35240, 10, 11, 20);
        LibSendMs::sendMsByCodeWithParent($simulation, 'MS46', 36540, 10, 11, 20, $mail_event['events'][0]['id']);
        SimulationService::saveEmailsAnalyze($simulation->id);
        $point = AssessmentCalculation::model()->findByAttributes(['point_id'=>$code_3323->id, 'sim_id'=>$simulation->id]);
        $this->assertEquals('0', $point->value);

        //good M71
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        EventsManager::getState($simulation, [[1, 1, 'activated', 35104, 'window_uid'=>20]]);
        EventsManager::startEvent($simulation, 'M71');
        $mail_event = EventsManager::getState($simulation, []);
        EventsManager::getState($simulation, [
            [1, 1, 'deactivated', 35120, 'window_uid'=>20],
            [10, 11, 'activated', 35120, ['mailId' => $mail_event['events'][0]['id']], 'window_uid'=>30],
        ]);
        MailBoxService::markReaded($mail_event['events'][0]['id']);
        EventsManager::getState($simulation, [
            [10, 11, 'deactivated', 35180, ['mailId' => $mail_event['events'][0]['id']], 'window_uid'=>30],
            [1, 1, 'activated', 35180, 'window_uid'=>20]
        ]);
        //LibSendMs::sendMsByCode($simulation, 'MS63', 35240, 10, 11, 20);
        LibSendMs::sendMsByCodeWithParent($simulation, 'MS63', 36540, 10, 11, 20, $mail_event['events'][0]['id']);
        SimulationService::saveEmailsAnalyze($simulation->id);
        $point = AssessmentCalculation::model()->findByAttributes(['point_id'=>$code_3323->id, 'sim_id'=>$simulation->id]);
        $this->assertEquals('0', $point->value);


}
    /*
     * Не читает спам
     */
    public function test_3325_true() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        EventsManager::startEvent($simulation, 'M60');
        EventsManager::getState($simulation, []);

        $point = HeroBehaviour::model()->findByAttributes([
            'code' => '3325'
        ]);

        SimulationService::saveEmailsAnalyze($simulation->id);

        $result = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);

        $this->assertEquals('0.00', $result->value);
    }

    /*
     * Читает спам
     */
    public function test_3325_false() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        EventsManager::startEvent($simulation, 'M60');
        $mail_event = EventsManager::getState($simulation, []);

        $spam = MailBox::model()->findByAttributes(['sim_id'=>$simulation->id, 'id'=>$mail_event['events'][0]['id']]);
        $spam->readed = 1;
        $spam->update();

        $point = HeroBehaviour::model()->findByAttributes([
            'code' => '3325'
        ]);

        SimulationService::saveEmailsAnalyze($simulation->id);

        $result = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);

        $this->assertEquals('-0.20', $result->value);
    }


}

