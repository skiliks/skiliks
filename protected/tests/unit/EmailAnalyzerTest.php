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
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        // move all not received emails to inbox
        $emailTemplates = MailTemplate::model()->findAll(
            " scenario_id = ".$simulation->scenario_id." AND code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
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
            'scenario_id' => $simulation->scenario_id,
            'code'        => '3313'
        ]);
        
        SimulationService::saveEmailsAnalyze($simulation);
        
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
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        // move all not received emails to inbox
        $emailTemplates = MailTemplate::model()->findAll(
            " scenario_id = ".$simulation->scenario_id." AND code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }
        // move all not received emails to inbox }

        $point = HeroBehaviour::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code' => '3313',
        ]);
        
        SimulationService::saveEmailsAnalyze($simulation);
        
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
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        $point_3322 = HeroBehaviour::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code'        => '3322',
        ]);
        
        $point_3324 = HeroBehaviour::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code'        => '3324',
        ]);

        $email = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M3');
        $email->readed = 1;
        $email->save();
        $email->refresh();
        $emailTask = $simulation->game_type->getMailTask(['code' => 'M3', 'wr' => 'R']);
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
        
        SimulationService::saveEmailsAnalyze($simulation);
        
        $result_3322 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3324->id,
        ]);
        
        $this->assertEquals($result_3322->value, $point_3322->scale, '3322 1');
        $this->assertEquals(0, $result_3324->value, '3324 1');
    }

    /**
     * Тест оценки 3322/3324:
     */
    public function test_3322_3324_OneFromOneEmailsNotPlannedRigthCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        $point_3322 = $simulation->game_type->getHeroBehaviour([
            'code' => '3322'
        ]);
        
        $point_3324 = $simulation->game_type->getHeroBehaviour([
            'code' => '3324'
        ]);

        // move all not received emails to inbox
        $emailTemplates = MailTemplate::model()->findAll(
            " code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' AND scenario_id=:scenario_id",
            ['scenario_id' => $simulation->game_type->getPrimaryKey()]
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }
        // move all not received emails to inbox }
        

        SimulationService::saveEmailsAnalyze($simulation);
        
        $result_3322 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3324->id,
        ]);
        
        $this->assertEquals(0, $result_3322->value, '3322 3');
        $this->assertEquals(0, $result_3324->value, '3324 3');
    }

    /**
     * Тест оценки 3322/3324:
     */
    public function test_3322_3324_OneFromOneEmailsPlannedWrongCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        $point_3322 = HeroBehaviour::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code'        => '3322',
        ]);
        
        $point_3324 = HeroBehaviour::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code'        => '3324',
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
        $emailTask = MailTask::model()->findByAttributes([
            'code'        => 'M3',
            'wr'          => 'W',
            'scenario_id' => $simulation->scenario_id,
        ]);
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
        
        SimulationService::saveEmailsAnalyze($simulation);
        
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
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        $point_3322 = HeroBehaviour::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code' => '3322',
        ]);
        
        $point_3324 = HeroBehaviour::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code' => '3324',
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
        
        SimulationService::saveEmailsAnalyze($simulation);
        
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
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        $point = HeroBehaviour::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code' => '3313',
        ]);

        $mails = MailBox::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        foreach ($mails as $mail) {
            $mail->readed = 0;
            $mail->save();
        }
        
        SimulationService::saveEmailsAnalyze($simulation);
        
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
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        //good M47
        $code_3323 = HeroBehaviour::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code'        => 3323,
        ]);

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

        SimulationService::saveEmailsAnalyze($simulation);

        $point = AssessmentCalculation::model()->findByAttributes([
            'point_id' => $code_3323->id,
            'sim_id'   => $simulation->id
        ]);

        $this->assertEquals('2', $point->value);

        //good M71
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

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
        SimulationService::saveEmailsAnalyze($simulation);
        $point = AssessmentCalculation::model()->findByAttributes(['point_id'=>$code_3323->id, 'sim_id'=>$simulation->id]);
        $this->assertEquals('2', $point->value);

        //good M47 and M71

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

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
        SimulationService::saveEmailsAnalyze($simulation);
        $point = AssessmentCalculation::model()->findByAttributes(['point_id'=>$code_3323->id, 'sim_id'=>$simulation->id]);
        $this->assertEquals('0', $point->value);

        //good M71
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

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
        SimulationService::saveEmailsAnalyze($simulation);
        $point = AssessmentCalculation::model()->findByAttributes(['point_id'=>$code_3323->id, 'sim_id'=>$simulation->id]);
        $this->assertEquals('0', $point->value);


}
    /*
     * Не читает спам
     */
    public function test_3325_true() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        EventsManager::startEvent($simulation, 'M60');
        EventsManager::getState($simulation, []);

        $point = HeroBehaviour::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code' => '3325',
        ]);

        SimulationService::saveEmailsAnalyze($simulation);

        $result = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);

        $this->assertEquals('0.00', $result->value);
    }

    /*
     * Читает спам
     */
    public function test_3325_false()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        EventsManager::startEvent($simulation, 'M60');
        $mail_event = EventsManager::getState($simulation, []);

        $spam = MailBox::model()->findByAttributes(['sim_id'=>$simulation->id, 'id'=>$mail_event['events'][0]['id']]);
        $spam->readed = 1;
        $spam->update();

        $point = HeroBehaviour::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code' => '3325',
        ]);

        SimulationService::saveEmailsAnalyze($simulation);

        $result = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);

        $this->assertEquals('-0.20', $result->value);
    }

    /**
     * 3311, Пользователь не читал и не писал писем
     */
    public function test_3311_case1()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $MY1_template = $simulation->game_type->getMailTemplate(['code' =>  'MY1']);
        $MY1_activity = $simulation->game_type->getActivity(['code' => 'AMY1']);
        $MY1_activityAction_id = $simulation->game_type->getActivityAction([
            'mail_id'     =>  $MY1_template->getPrimaryKey(),
            'leg_type'    => ActivityAction::LEG_TYPE_INBOX,
            'activity_id' => $MY1_activity->id,
        ])->getPrimaryKey();

        // лог {
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = 'Inbox_leg';
        $log->leg_action = 'MY1';
        $log->activity_action_id = $MY1_activityAction_id;
        $log->duration = '01:11:00';
        $log->save();
        // лог }

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3311();

        $this->assertEquals(0, $result['positive']);
        $this->assertEquals(1, $result['case']); // 'case' - option for test reasons only
    }

    /**
     * Service method
     * Добавляет достаточное количество отправленных и прочитанных писем в MailBox,
     * чтоб началось оценивание сессий работы с почтой в рамках поведения 3311
     *
     * @param Simulation $simulation
     */
    protected function setEmailFor3311Tests($simulation)
    {
        LibSendMs::sendMsByCode($simulation, 'MS10');
        LibSendMs::sendMsByCode($simulation, 'MS10');
        LibSendMs::sendMsByCode($simulation, 'MS10');
        LibSendMs::sendMsByCode($simulation, 'MS10');
        LibSendMs::sendMsByCode($simulation, 'MS10');

        LibSendMs::sendMsByCode($simulation, 'MS10');
        LibSendMs::sendMsByCode($simulation, 'MS10');
        LibSendMs::sendMsByCode($simulation, 'MS10');
        LibSendMs::sendMsByCode($simulation, 'MS10');
        LibSendMs::sendMsByCode($simulation, 'MS10');

        LibSendMs::sendMsByCode($simulation, 'MS10');

        $m5  = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M5');
        $m5->readed = 1;
        $m5->save();

        $m6  = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M6');
        $m6->readed = 1;
        $m6->save();

        $m7  = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M7');
        $m7->readed = 1;
        $m7->save();

        $m8  = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M8');
        $m8->readed = 1;
        $m8->save();

        $m9  = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M9');
        $m9->readed = 1;
        $m9->save();

        $m10 = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M10');
        $m10->readed = 1;
        $m10->save();
    }

    /**
     * 3311, Пользователь прочёл и написал пдостаточно писем, но работал спочтой дольше 90 мин
     */
    public function test_3311_case2()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $MY1_template = $simulation->game_type->getMailTemplate(['code' =>  'MY1']);
        $MY1_activity = $simulation->game_type->getActivity(['code' => 'AMY1']);
        $MY1_activityAction_id = $simulation->game_type->getActivityAction([
            'mail_id'     =>  $MY1_template->getPrimaryKey(),
            'leg_type'    => ActivityAction::LEG_TYPE_INBOX,
            'activity_id' => $MY1_activity->id,
        ])->getPrimaryKey();

        // лог {
        // 2 лога - чтобы проверить что их длительность просуммируется
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = 'Inbox_leg';
        $log->leg_action = 'MY1';
        $log->activity_action_id = $MY1_activityAction_id;
        $log->duration = '01:00:00';
        $log->save();

        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = 'Inbox_leg';
        $log->leg_action = 'MY1';
        $log->activity_action_id = $MY1_activityAction_id;
        $log->duration = '00:33:00';
        $log->save();
        // лог }

        $this->setEmailFor3311Tests($simulation);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3311();

        $this->assertEquals(0, $result['positive']);
        $this->assertEquals(2, $result['case']); // 'case' - option for test reasons only
    }

    /**
     * 3311, Пользователь прочёл и написал пдостаточно писем, но работал с почтой 1 раз, и правильное количество минут - 60 мин
     */
    public function test_3311_case3()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $MY1_template = $simulation->game_type->getMailTemplate(['code' =>  'MY1']);
        $MY1_activity = $simulation->game_type->getActivity(['code' => 'AMY1']);
        $MY1_activityAction_id = $simulation->game_type->getActivityAction([
            'mail_id'     =>  $MY1_template->getPrimaryKey(),
            'leg_type'    => ActivityAction::LEG_TYPE_INBOX,
            'activity_id' => $MY1_activity->id,
        ])->getPrimaryKey();

        // лог {
        // 2 лога - чтобы проверить что их длительность просуммируется
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = 'Inbox_leg';
        $log->leg_action = 'MY1';
        $log->activity_action_id = $MY1_activityAction_id;
        $log->duration = '01:00:00';
        $log->save();
        // лог }

        $this->setEmailFor3311Tests($simulation);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3311();

        $this->assertEquals(0, $result['positive']);
        $this->assertEquals(3, $result['case']); // 'case' - option for test reasons only
    }

    /**
     * 3311, Пользователь прочёл и написал пдостаточно писем, но работал с почтой много (6) раз, и правильное количество минут - 60 мин
     */
    public function test_3311_case4()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $D2_template = $simulation->game_type->getDocumentTemplate(['code' =>  'D2']);
        $D2_activity = $simulation->game_type->getActivity(['code' => 'T2']);
        $D2_activityAction_id = $simulation->game_type->getActivityAction([
            'document_id' => $D2_template->getPrimaryKey(),
            'leg_type'    => ActivityAction::LEG_TYPE_DOCUMENTS,
            'activity_id' => $D2_activity->id,
        ])->getPrimaryKey();

        $MY1_template = $simulation->game_type->getMailTemplate(['code' =>  'MY1']);
        $MY1_activity = $simulation->game_type->getActivity(['code' => 'AMY1']);
        $MY1_activityAction_id = $simulation->game_type->getActivityAction([
            'mail_id'     => $MY1_template->getPrimaryKey(),
            'leg_type'    => ActivityAction::LEG_TYPE_INBOX,
            'activity_id' => $MY1_activity->id,
        ])->getPrimaryKey();

        // лог {
        // 2 лога - чтобы проверить что их длительность просуммируется
        for ($i = 0; $i < 5; $i++) {
            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = 'Inbox_leg';
            $log->leg_action = 'MY1';
            $log->activity_action_id = $MY1_activityAction_id;
            $log->duration = '00:15:00';
            $log->save();

            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = 'Window';
            $log->leg_action = 'MY1';
            $log->activity_action_id = $D2_activityAction_id;
            $log->duration = '00:10:00';
            $log->save();
        }
        // лог }

        $this->setEmailFor3311Tests($simulation);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3311();

        $this->assertEquals(0, $result['positive']);
        $this->assertEquals(4, $result['case']); // 'case' - option for test reasons only
    }

    /**
     * 3311, Пользователь прочёл и написал пдостаточно писем, работал с почтой нормальнео число (2) раз,
     * и правильное количество минут - 60 мин
     */
    public function test_3311_case5()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $D2_template = $simulation->game_type->getDocumentTemplate(['code' =>  'D2']);
        $D2_activity = $simulation->game_type->getActivity(['code' => 'T2']);
        $D2_activityAction_id = $simulation->game_type->getActivityAction([
            'document_id' => $D2_template->getPrimaryKey(),
            'leg_type'    => ActivityAction::LEG_TYPE_DOCUMENTS,
            'activity_id' => $D2_activity->id,
        ])->getPrimaryKey();

        $MY1_template = $simulation->game_type->getMailTemplate(['code' =>  'MY1']);
        $MY1_activity = $simulation->game_type->getActivity(['code' => 'AMY1']);
        $MY1_activityAction_id = $simulation->game_type->getActivityAction([
            'mail_id'     => $MY1_template->getPrimaryKey(),
            'leg_type'    => ActivityAction::LEG_TYPE_INBOX,
            'activity_id' => $MY1_activity->id,
        ])->getPrimaryKey();

        // лог {
        // 2 лога - чтобы проверить что их длительность просуммируется
        for ($i = 0; $i < 2; $i++) {
            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = 'Inbox_leg';
            $log->leg_action = 'MY1';
            $log->activity_action_id = $MY1_activityAction_id;
            $log->duration = '00:30:00';
            $log->save();

            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = 'Window';
            $log->leg_action = 'MY1';
            $log->activity_action_id = $D2_activityAction_id;
            $log->duration = '00:10:00';
            $log->save();
        }
        // лог }

        $this->setEmailFor3311Tests($simulation);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3311();

        $this->assertEquals($result['obj']->scale, $result['positive']);
        $this->assertEquals(5, $result['case']); // 'case' - option for test reasons only
    }

    /**
     * 3311, Пользователь прочёл и написал пдостаточно писем, работал с почтой нормальнео число (2) раз,
     * и правильное количество минут - 75 мин
     */
    public function test_3311_case6()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $D2_template = $simulation->game_type->getDocumentTemplate(['code' =>  'D2']);
        $D2_activity = $simulation->game_type->getActivity(['code' => 'T2']);
        $D2_activityAction_id = $simulation->game_type->getActivityAction([
            'document_id' => $D2_template->getPrimaryKey(),
            'leg_type'    => ActivityAction::LEG_TYPE_DOCUMENTS,
            'activity_id' => $D2_activity->id,
        ])->getPrimaryKey();

        $MY1_template = $simulation->game_type->getMailTemplate(['code' =>  'MY1']);
        $MY1_activity = $simulation->game_type->getActivity(['code' => 'AMY1']);
        $MY1_activityAction_id = $simulation->game_type->getActivityAction([
            'mail_id'     => $MY1_template->getPrimaryKey(),
            'leg_type'    => ActivityAction::LEG_TYPE_INBOX,
            'activity_id' => $MY1_activity->id,
        ])->getPrimaryKey();

        // лог {
        // 2 лога - чтобы проверить что их длительность просуммируется
        for ($i = 0; $i < 3; $i++) {
            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = 'Inbox_leg';
            $log->leg_action = 'MY1';
            $log->activity_action_id = $MY1_activityAction_id;
            $log->duration = '00:25:00';
            $log->save();

            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = 'Window';
            $log->leg_action = 'MY1';
            $log->activity_action_id = $D2_activityAction_id;
            $log->duration = '00:10:00';
            $log->save();
        }
        // лог }

        $this->setEmailFor3311Tests($simulation);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3311();

        $this->assertEquals($result['obj']->scale*(2/3), $result['positive']);
        $this->assertEquals(5, $result['case']); // 'case' - option for test reasons only
    }

    /**
     * 3311, Пользователь прочёл и написал пдостаточно писем, работал с почтой нормальнео число (2) раз,
     * и правильное количество минут - 90 мин
     */
    public function test_3311_case7()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $D2_template = $simulation->game_type->getDocumentTemplate(['code' =>  'D2']);
        $D2_activity = $simulation->game_type->getActivity(['code' => 'T2']);
        $D2_activityAction_id = $simulation->game_type->getActivityAction([
            'document_id' => $D2_template->getPrimaryKey(),
            'leg_type'    => ActivityAction::LEG_TYPE_DOCUMENTS,
            'activity_id' => $D2_activity->id,
        ])->getPrimaryKey();

        $MY1_template = $simulation->game_type->getMailTemplate(['code' =>  'MY1']);
        $MY1_activity = $simulation->game_type->getActivity(['code' => 'AMY1']);
        $MY1_activityAction_id = $simulation->game_type->getActivityAction([
            'mail_id'     => $MY1_template->getPrimaryKey(),
            'leg_type'    => ActivityAction::LEG_TYPE_INBOX,
            'activity_id' => $MY1_activity->id,
        ])->getPrimaryKey();

        // лог {
        // 2 лога - чтобы проверить что их длительность просуммируется
        for ($i = 0; $i < 3; $i++) {
            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = 'Inbox_leg';
            $log->leg_action = 'MY1';
            $log->activity_action_id = $MY1_activityAction_id;
            $log->duration = '00:30:00';
            $log->save();

            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = 'Window';
            $log->leg_action = 'MY1';
            $log->activity_action_id = $D2_activityAction_id;
            $log->duration = '00:10:00';
            $log->save();
        }
        // лог }

        $this->setEmailFor3311Tests($simulation);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3311();

        $this->assertEquals($result['obj']->scale*(1/3), $result['positive']);
        $this->assertEquals(5, $result['case']); // 'case' - option for test reasons only
    }
}

