<?php

/**
 *
 * @author slavka
 */
class EmailAnalyzerUnitTest extends CDbTestCase
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
        $log->start_time = '9:00:00';
        $log->end_time = '10:11:00';
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

        $m8  = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M9');
        $m8->readed = 1;
        $m8->save();

        $m8  = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M10');
        $m8->readed = 1;
        $m8->save();
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
        $log->leg_type = ActivityAction::LEG_TYPE_INBOX;
        $log->leg_action = 'MY1';
        $log->activity_action_id = $MY1_activityAction_id;
        $log->start_time = '11:00:00';
        $log->end_time = '11:00:20';
        $log->duration = '01:00:00';
        $log->save();

        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_INBOX;
        $log->leg_action = 'MY1';
        $log->activity_action_id = $MY1_activityAction_id;
        $log->start_time = '11:00:00';
        $log->end_time = '11:00:20';

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
        $log->leg_type = ActivityAction::LEG_TYPE_INBOX;
        $log->leg_action = 'MY1';
        $log->activity_action_id = $MY1_activityAction_id;
        $log->start_time = '11:00:00';
        $log->end_time = '11:11:00';
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
            $log->leg_type = ActivityAction::LEG_TYPE_INBOX;
            $log->leg_action = 'MY1';
            $log->activity_action_id = $MY1_activityAction_id;
            $log->start_time = '11:00:00';
            $log->end_time = '11:11:00';
            $log->duration = '00:15:00';
            $log->save();

            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = ActivityAction::LEG_TYPE_DOCUMENTS;
            $log->leg_action = 'AD2';
            $log->activity_action_id = $D2_activityAction_id;
            $log->start_time = '11:00:00';
            $log->end_time = '11:11:00';
            $log->duration = '00:10:00';
            $log->save();
        }
        // лог }

        $this->setEmailFor3311Tests($simulation);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3311();

        $this->assertEquals(4, $result['case']); // 'case' - option for test reasons only
        $this->assertEquals(0, $result['positive']);

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
            $log->leg_type = ActivityAction::LEG_TYPE_INBOX;
            $log->leg_action = 'MY1';
            $log->activity_action_id = $MY1_activityAction_id;
            $log->start_time = '11:00:00';
            $log->end_time = '11:11:00';
            $log->duration = '00:30:00';
            $log->save();

            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = ActivityAction::LEG_TYPE_DOCUMENTS;
            $log->leg_action = 'AD2';
            $log->activity_action_id = $D2_activityAction_id;
            $log->start_time = '11:00:00';
            $log->end_time = '11:11:00';
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
            $log->leg_type = ActivityAction::LEG_TYPE_INBOX;
            $log->leg_action = 'MY1';
            $log->activity_action_id = $MY1_activityAction_id;
            $log->start_time = "1$i:00:00";
            $log->end_time = "1$i:25:00";
            $log->duration = '00:25:00';
            $log->save();

            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = ActivityAction::LEG_TYPE_DOCUMENTS;
            $log->leg_action = 'AD2';
            $log->activity_action_id = $D2_activityAction_id;
            $log->start_time = "1$i:30:00";
            $log->end_time = "1$i:40:00";
            $log->duration = '00:10:00';
            $log->save();
        }
        // лог }

        $this->setEmailFor3311Tests($simulation);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3311();

        $this->assertEquals(5, $result['case']); // 'case' - option for test reasons only
        $this->assertEquals($result['obj']->scale*(2/3), $result['positive']);
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
            $log->leg_type = ActivityAction::LEG_TYPE_INBOX;
            $log->leg_action = 'MY1';
            $log->activity_action_id = $MY1_activityAction_id;
            $log->start_time = "1$i:00:00";
            $log->end_time = "1$i:30:00";
            $log->duration = '00:30:00';
            $log->save();

            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = ActivityAction::LEG_TYPE_DOCUMENTS;
            $log->leg_action = 'AD2';
            $log->activity_action_id = $D2_activityAction_id;
            $log->start_time = "1$i:00:00";
            $log->end_time = "1$i:10:00";
            $log->duration = '00:10:00';
            $log->save();
        }
        // лог }

        $this->setEmailFor3311Tests($simulation);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3311();


        $this->assertEquals(5, $result['case']); // 'case' - option for test reasons only
        $this->assertEquals($result['obj']->scale*(1/3), $result['positive']);
    }

    /**
     * 3311, Пользователь прочёл и написал пдостаточно писем, работал с почтой много (8) раз,
     * и правильное количество минут - 60 мин
     * после 11:00 только 2 сессии - они учитываются
     */
    public function test_3311_case8()
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
            $log->leg_type = ActivityAction::LEG_TYPE_INBOX;
            $log->leg_action = 'MY1';
            $log->activity_action_id = $MY1_activityAction_id;
            $log->start_time = '9:00:00';
            $log->end_time = '11:05:00';
            $log->duration = '00:05:00';
            $log->save();

            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = ActivityAction::LEG_TYPE_INBOX;
            $log->leg_action = 'AD2';
            $log->activity_action_id = $D2_activityAction_id;
            $log->start_time = '9:00:00';
            $log->end_time = '11:05:00';
            $log->duration = '00:05:00';
            $log->save();
        }

        for ($i = 0; $i < 2; $i++) {
            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = ActivityAction::LEG_TYPE_INBOX;
            $log->leg_action = 'MY1';
            $log->activity_action_id = $MY1_activityAction_id;
            $log->start_time = '12:00:00';
            $log->end_time = '12:05:00';
            $log->duration = '00:05:00';
            $log->save();

            $log = new LogActivityActionAgregated();
            $log->sim_id = $simulation->id;
            $log->leg_type = ActivityAction::LEG_TYPE_DOCUMENTS;
            $log->leg_action = 'AD2';
            $log->activity_action_id = $D2_activityAction_id;
            $log->start_time = '12:00:00';
            $log->end_time = '12:05:00';
            $log->duration = '00:05:00';
            $log->save();
        }
        // лог }

        $this->setEmailFor3311Tests($simulation);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3311();

        $this->assertEquals(5, $result['case']); // 'case' - option for test reasons only
        $this->assertEquals($result['obj']->scale, $result['positive']);
    }

    /**
     * 3322 - Пользователь отправил одно правильное письмо MS со всеми нужными копиями
     */
    public function test_3332_case1()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // prepare data {
        $sample = null;

        $rightMsEmails = MailTemplate::model()
            ->with('subject_obj')
            ->findAll(sprintf(
                " t.code LIKE 'MS%s' AND subject_obj.wr = 'R' AND t.scenario_id = %s",
                '%',
                $simulation->scenario_id
            ));

        $count = 0;
        foreach ($rightMsEmails as $rightMsEmail) {
            if (0 < MailTemplateCopy::model()->count(sprintf('mail_id = %s ', $rightMsEmail->id))) {
                $sample = $rightMsEmail;
                $count++;
            }
        }

        $this->assertNotNull($sample);
        // prepare data }

        // send MS
        LibSendMs::sendMsByCode($simulation, $sample->code);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3332();

        $this->assertEquals($result['obj']->scale/$count, $result['positive']);
    }

    /**
     * 3322 - Пользователь отправил все правильные письма MS со всеми нужными копиями
     */
    public function test_3332_case2()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // prepare data {
        $rightMsEmails = MailTemplate::model()
            ->with('subject_obj')
            ->findAll(sprintf(
                " t.code LIKE 'MS%s' AND subject_obj.wr = 'R' AND t.scenario_id = %s",
                '%',
                $simulation->scenario_id
            ));

        foreach ($rightMsEmails as $rightMsEmail) {
            if (0 < MailTemplateCopy::model()->count(sprintf('mail_id = %s ', $rightMsEmail->id))) {
                // send MS
                LibSendMs::sendMsByCode($simulation, $rightMsEmail->code);
            }
        }
        // prepare data }

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3332();

        $this->assertEquals($result['obj']->scale, $result['positive']);
    }

    /**
     * 3322 - Пользователь не отправил ни одного письма MS со всеми нужными копиями
     */
    public function test_3332_case3()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3332();

        $this->assertEquals(0, $result['positive']);
    }

    /**
     * 3322 - Пользователь отправил одно правильное письмо MS где должны быть копии,
     * но копии пусты
     */
    public function test_3332_case4()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // prepare data {
        $sample = null;

        $rightMsEmails = MailTemplate::model()
            ->with('subject_obj')
            ->findAll(sprintf(
                " t.code LIKE 'MS%s' AND subject_obj.wr = 'R' AND t.scenario_id = %s",
                '%',
                $simulation->scenario_id
            ));

        $count = 0;
        foreach ($rightMsEmails as $rightMsEmail) {
            if (0 < MailTemplateCopy::model()->count(sprintf('mail_id = %s ', $rightMsEmail->id))) {
                $sample = $rightMsEmail;
                $count++;
            }
        }

        $this->assertNotNull($sample);
        // prepare data }

        // send MS
        $sendEmail = LibSendMs::sendMsByCode($simulation, $sample->code);

        // remove copies
        $this->assertNotNull($sendEmail);
        $emailCopies = MailCopy::model()->findAll(' mail_id = '.$sendEmail->id);
        foreach ($emailCopies as $emailCopy) {
            $emailCopy->delete();
        }

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3332();

        $this->assertEquals(0, $result['positive']);
    }

    /**
     * 3322 - Пользователь отправил одно правильное письмо MS где должны быть копии,
     * в копии есть лишние получатели
     */
    public function test_3332_case5()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // prepare data {
        $sample = null;

        $rightMsEmails = MailTemplate::model()
            ->with('subject_obj')
            ->findAll(sprintf(
                " t.code LIKE 'MS%s' AND subject_obj.wr = 'R' AND t.scenario_id = %s",
                '%',
                $simulation->scenario_id
            ));

        $count = 0;
        foreach ($rightMsEmails as $rightMsEmail) {
            if (0 < MailTemplateCopy::model()->count(sprintf('mail_id = %s ', $rightMsEmail->id))) {
                $sample = $rightMsEmail;
                $count++;
            }
        }

        $this->assertNotNull($sample);
        // prepare data }

        // send MS
        $sendEmail = LibSendMs::sendMsByCode($simulation, $sample->code);

        // add extra copies {
        $this->assertNotNull($sendEmail);

        $characters = Character::model()->findAll("scenario_id = $simulation->scenario_id ");
        foreach ($characters as $character) {
            $mailCopy = new MailCopy();
            $mailCopy->mail_id     = $sendEmail->id;
            $mailCopy->receiver_id = $character->id;
            try {
                $mailCopy->save();
            } catch (CDbException $e) {}
        }
        // add extra copies }

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3332();

        $this->assertEquals(0, $result['positive']);
    }

    /**
     * 3322 - Пользователь отправил правильные письма MS10, MS25, MS40, MS40
     * все MS c полным совпадением
     */
    public function test_3332_case6()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // prepare data {
        LibSendMs::sendMsByCode($simulation, 'MS10');
        LibSendMs::sendMsByCode($simulation, 'MS25');
        LibSendMs::sendMsByCode($simulation, 'MS40');
        LibSendMs::sendMsByCode($simulation, 'MS40');

        // prepare data }

        $emailAnalyzer = new EmailAnalyzer($simulation);

        $result = $emailAnalyzer->check_3332();

        $this->assertEquals(0.375, $result['positive']);
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 11 W, 15R, 0N => 0 баллов
     */
    public function testCalculateAgregatedPointsFor3326_0pointsCase1()
    {
        //$this->markTestSkipped();

        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // init MS emails:
        $ms[] = LibSendMs::sendMs($simulation, 'MS10');
        $ms[] = LibSendMs::sendMs($simulation, 'MS21');
        $ms[] = LibSendMs::sendMs($simulation, 'MS22');
        $ms[] = LibSendMs::sendMs($simulation, 'MS23');
        $ms[] = LibSendMs::sendMs($simulation, 'MS27');
        $ms[] = LibSendMs::sendMs($simulation, 'MS30');
        $ms[] = LibSendMs::sendMs($simulation, 'MS32');
        $ms[] = LibSendMs::sendMs($simulation, 'MS49');
        $ms[] = LibSendMs::sendMs($simulation, 'MS50');
        $ms[] = LibSendMs::sendMs($simulation, 'MS54');
        $ms[] = LibSendMs::sendMs($simulation, 'MS58');

        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS28');
        $ms[] = LibSendMs::sendMs($simulation, 'MS35');
        $ms[] = LibSendMs::sendMs($simulation, 'MS36');
        $ms[] = LibSendMs::sendMs($simulation, 'MS37');
        $ms[] = LibSendMs::sendMs($simulation, 'MS39');
        $ms[] = LibSendMs::sendMs($simulation, 'MS40');
        $ms[] = LibSendMs::sendMs($simulation, 'MS48');
        $ms[] = LibSendMs::sendMs($simulation, 'MS51');
        $ms[] = LibSendMs::sendMs($simulation, 'MS53');
        $ms[] = LibSendMs::sendMs($simulation, 'MS55');
        $ms[] = LibSendMs::sendMs($simulation, 'MS37');
        $ms[] = LibSendMs::sendMs($simulation, 'MS60');
        $ms[] = LibSendMs::sendMs($simulation, 'MS61');
        $ms[] = LibSendMs::sendMs($simulation, 'MS69');

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }


        EventsManager::processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalyze($simulation);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(0, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0 W, 1R, 0N => 0 баллов
     */
    public function testCalculateAgregatedPointsFor3326_0pointsCase2()
    {
        //$this->markTestSkipped();

        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // init MS emails:
        $ms[20] = LibSendMs::sendMs($simulation, 'MS20');

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }


        EventsManager::processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalyze($simulation);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(0, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0W, 0R, 0N => 0 баллов
     */
    public function testCalculateAgregatedPointsFor3326_0pointsCase3()
    {
        //$this->markTestSkipped();

        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // calculate point total scores
        SimulationService::saveEmailsAnalyze($simulation);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(0, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0 W, 1R - 15 раз, 0N => 0 баллов
     */
    public function testCalculateAgregatedPointsFor3326_0pointsCase4()
    {
        //$this->markTestSkipped();

        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // init MS emails:
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }


        EventsManager::processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalyze($simulation);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(0, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0W, 14R, 0N => 2 балла
     */
    public function testCalculateAgregatedPointsFor3326_2pointsCase1()
    {
        //$this->markTestSkipped();

        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // init MS emails:
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS28');
        $ms[] = LibSendMs::sendMs($simulation, 'MS35');
        $ms[] = LibSendMs::sendMs($simulation, 'MS36');
        $ms[] = LibSendMs::sendMs($simulation, 'MS37');
        $ms[] = LibSendMs::sendMs($simulation, 'MS39');
        $ms[] = LibSendMs::sendMs($simulation, 'MS48');
        $ms[] = LibSendMs::sendMs($simulation, 'MS51');
        $ms[] = LibSendMs::sendMs($simulation, 'MS53');
        $ms[] = LibSendMs::sendMs($simulation, 'MS55');
        $ms[] = LibSendMs::sendMs($simulation, 'MS57');
        $ms[] = LibSendMs::sendMs($simulation, 'MS60');
        $ms[] = LibSendMs::sendMs($simulation, 'MS61');
        $ms[] = LibSendMs::sendMs($simulation, 'MS69');

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }


        EventsManager::processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalyze($simulation);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(2, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 3W, 14R, 0N => 1 балл
     */
    public function testCalculateAgregatedPointsFor3326Part1pointsCase1()
    {
        //$this->markTestSkipped();

        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // init MS emails:
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS28');
        $ms[] = LibSendMs::sendMs($simulation, 'MS35');
        $ms[] = LibSendMs::sendMs($simulation, 'MS36');
        $ms[] = LibSendMs::sendMs($simulation, 'MS37');
        $ms[] = LibSendMs::sendMs($simulation, 'MS39');
        $ms[] = LibSendMs::sendMs($simulation, 'MS48');
        $ms[] = LibSendMs::sendMs($simulation, 'MS51');
        $ms[] = LibSendMs::sendMs($simulation, 'MS53');
        $ms[] = LibSendMs::sendMs($simulation, 'MS55');
        $ms[] = LibSendMs::sendMs($simulation, 'MS57');
        $ms[] = LibSendMs::sendMs($simulation, 'MS60');
        $ms[] = LibSendMs::sendMs($simulation, 'MS61');
        $ms[] = LibSendMs::sendMs($simulation, 'MS69');

        $ms[] = LibSendMs::sendMs($simulation, 'MS54');
        $ms[] = LibSendMs::sendMs($simulation, 'MS54');
        $ms[] = LibSendMs::sendMs($simulation, 'MS58');

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }


        EventsManager::processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalyze($simulation);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);

        // assertions:
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(1, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0W, 14R, 0N (total R = 13) => 2 балла
     */
    public function testCalculateAgregatedPointsFor3326Part2pointsCase2()
    {
        //s//$this->markTestSkipped();

        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // init MS emails:

        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS28');
        $ms[] = LibSendMs::sendMs($simulation, 'MS35');
        $ms[] = LibSendMs::sendMs($simulation, 'MS36');
        $ms[] = LibSendMs::sendMs($simulation, 'MS37');
        $ms[] = LibSendMs::sendMs($simulation, 'MS39');
        $ms[] = LibSendMs::sendMs($simulation, 'MS40');
        $ms[] = LibSendMs::sendMs($simulation, 'MS48');
        $ms[] = LibSendMs::sendMs($simulation, 'MS51');
        $ms[] = LibSendMs::sendMs($simulation, 'MS53');
        $ms[] = LibSendMs::sendMs($simulation, 'MS55');
        $ms[] = LibSendMs::sendMs($simulation, 'MS57');
        $ms[] = LibSendMs::sendMs($simulation, 'MS61');
        $ms[] = LibSendMs::sendMs($simulation, 'MS69');

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }


        EventsManager::processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalyze($simulation);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);

        // assertions:
        $this->assertNotEquals(count($assessments), 1, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(2, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет правильность оценки по 3326
     * Случай когда 0W, 12R, 1N => 0 баллов
     */
    public function testCalculateAgregatedPointsFor3326_0pointsCase5()
    {
        //$this->markTestSkipped();

        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // init MS emails:
        $ms[] = LibSendMs::sendMs($simulation, 'MS20');
        $ms[] = LibSendMs::sendMs($simulation, 'MS28');
        $ms[] = LibSendMs::sendMs($simulation, 'MS35');
        $ms[] = LibSendMs::sendMs($simulation, 'MS36');
        $ms[] = LibSendMs::sendMs($simulation, 'MS37');
        $ms[] = LibSendMs::sendMs($simulation, 'MS39');
        $ms[] = LibSendMs::sendMs($simulation, 'MS48');
        $ms[] = LibSendMs::sendMs($simulation, 'MS51');
        $ms[] = LibSendMs::sendMs($simulation, 'MS53');
        $ms[] = LibSendMs::sendMs($simulation, 'MS55');
        $ms[] = LibSendMs::sendMs($simulation, 'MS57');
        $ms[] = LibSendMs::sendMs($simulation, 'MS60');
        $ms[] = LibSendMs::sendMs($simulation, 'MS79');

        // set-up logs {
        $logs = [];
        $i = 1;
        $time = 32500;
        foreach($ms as $email) {
            $logs[] = [10, 13, 'activated'  , $time, 'window_uid' => $i];
            $time = $time + 100;
            $logs[] = [10, 13, 'deactivated', $time, 'window_uid' => $i, 4 => ['mailId' => $email->id]];
            $i++;
        }
        // set-up logs }

        EventsManager::processLogs($simulation, $logs);

        // calculate point total scores
        SimulationService::saveEmailsAnalyze($simulation);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);

        // assertions:

        $this->assertNotEquals(count($assessments), 1, 'No assessments!');

        $is_3326_scored = false;

        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3326') {
                $this->assertEquals(0, $assessment->value, '3326 value!');
                $is_3326_scored = true;
            }
        }

        $this->assertTrue($is_3326_scored, '3326 not scored!');
    }

    /**
     * Проверяет что, если пользователь отверил всем на письма от Скоробей (MS60),
     * то за 3333 он получит максимальный балл
     */
    public function testCalculateAggregatedPointsFor3333_OK_case1()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // activate mainScreen
        $logs[] = [1, 1, 'activated' , 34200, 'window_uid' => 100];
        EventsManager::processLogs($simulation, $logs);

        // we allow user reply all by MS60
        LibSendMs::sendMsByCode($simulation, 'MS60', 35000, 1, 1, 100);

        // calculate point total scores
        SimulationService::saveEmailsAnalyze($simulation);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        $heroBehaviour = $simulation->game_type->getHeroBehaviour(['code' => '3333']);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);

        $is_3333_scored = true;

        // assertions:
        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3333') {
                $this->assertEquals($heroBehaviour->scale, $assessment->value, '3333 value!');
                $is_3333_scored = true;
            }
        }

        $this->assertTrue($is_3333_scored, '3326 not scored!');
    }

    /**
     * Проверяет что, если пользователь отверил всем на письмо MS20 (не должен отвечать всем по этому письму),
     * то за 3333 он получит "0"
     */
    public function testCalculateAggregatedPointsFor3333_bad_case1()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // activate mainScreen
        $logs[] = [1, 1, 'activated' , 34200, 'window_uid' => 100];
        EventsManager::processLogs($simulation, $logs);

        // we allow user reply all by MS60
        $ms = LibSendMs::sendMsByCode(
            $simulation,
            'MS20', // code
            35000,  // time
            1,      // windowId
            1,      // subWindowUid
            100,   // windowUid
            10,     // duration
            false,  // isDraft
            MailBox::TYPE_REPLY_ALL  // letter_type
        );

        // calculate point total scores
        SimulationService::saveEmailsAnalyze($simulation);
        SimulationService::copyMailInboxOutboxScoreToAssessmentAggregated($simulation->id);

        // check calculation
        $assessments = AssessmentAggregated::model()->findAll('sim_id =:id',[
            'id' => $simulation->id
        ]);

        $is_3333_scored = true;

        // assertions:
        foreach ($assessments as $assessment) {
            if ($assessment->point->code === '3333') {
                $this->assertEquals(0, $assessment->value, '3333 value!');
                $is_3333_scored = true;
            }
        }

        $this->assertTrue($is_3333_scored, '3326 not scored!');
    }

    /**
     * @return MailBox|null
     */
    public function testEmailAnalyzerAssessmentForLiteSim()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_LITE;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        SimulationService::setSimulationClockTime($simulation, 10, 50);

        EventsManager::getState($simulation, []);
        EventsManager::getState($simulation, []);
        EventsManager::getState($simulation, []);
        EventsManager::getState($simulation, []);
        EventsManager::getState($simulation, []);

        $characterSomebody = $simulation->game_type->getCharacter(['code' => 32]);

        // email-1 {
        $mailM71 = MailBox::model()->findByAttributes(['sim_id' => $simulation->id, 'code' => 'M71']);
        $characterLudovkina = $simulation->game_type->getCharacter(['code' => 13]);
        $subjectForCharacter13 = $simulation->game_type->getCommunicationTheme([
            'character_id'  => $characterLudovkina->id,
            'letter_number' => 'MS63',
        ]);
        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray($characterSomebody->id); // Неизвестная
        $sendMailOptions->groupId    = MailBox::FOLDER_DRAFTS_ID;
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subjectForCharacter13->id;
        $sendMailOptions->messageId  = $mailM71->id;

        MailBoxService::sendMessagePro($sendMailOptions);
        // email-1 }

        // email-2 {
        $mailM71 = MailBox::model()->findByAttributes(['sim_id' => $simulation->id, 'code' => 'M71']);
        $characterLudovkina = $simulation->game_type->getCharacter(['code' => 13]);
        $subjectForCharacter13 = $simulation->game_type->getCommunicationTheme([
            'character_id'  => $characterLudovkina->id,
            'letter_number' => 'MS63',
        ]);
        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray($characterSomebody->id); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->groupId    = MailBox::FOLDER_OUTBOX_ID;
        $sendMailOptions->time       = '09:02';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subjectForCharacter13->id;
        $sendMailOptions->messageId  = $mailM71->id;

        MailBoxService::sendMessagePro($sendMailOptions);
        // email-2 }

        // email-3 {
        $mailM47 = MailBox::model()->findByAttributes(['sim_id' => $simulation->id, 'code' => 'M47']);
        $characterWife = $simulation->game_type->getCharacter(['code' => 25]);
        $subjectForCharacter25 = $simulation->game_type->getCommunicationTheme([
            'character_id' => $characterWife->id,
            'text'         => 'данные по рынку, срочно нужна помощь!',
        ]);
        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray($characterSomebody->id); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->groupId    = MailBox::FOLDER_DRAFTS_ID;
        $sendMailOptions->time       = '09:03';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subjectForCharacter25->id;
        $sendMailOptions->messageId  = $mailM47->id;

        MailBoxService::sendMessagePro($sendMailOptions);
        // email-3 }

//        var_dump($simulation->id);
//        die;

        SimulationService::simulationStop($simulation);
    }
}

