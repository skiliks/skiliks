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
        
        SimulationService::saveEmailsAnalyze($simulation->id);
        
        $result = AssessmentCalculation::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);
        
        $this->assertEquals($result->value, 0);
    }
}

