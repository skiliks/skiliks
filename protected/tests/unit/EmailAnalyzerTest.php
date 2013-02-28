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
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);

        // move all not received emails to inbox
        $emailTemplates = MailTemplateModel::model()->findAll(
            " code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }
        // move all not received emails to inbox }
        
        // mark all inbox emails as read
        MailBoxModel::model()->updateAll([
                'readed' => 1
            ], 
            'sim_id = :sim_id AND group_id = :group_id', 
            [
                'sim_id'   => $simulation->id,
                'group_id' => 1
            ]
        );
        
        $point = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3313'
        ]);
        
        SimulationService::saveEmailsAnalize($simulation->id);
        
        $result = SimulationMailPoint::model()->findByAttributes([
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
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);
        
        $randomSpamMailTemplate = MailTemplateModel::model()->findByAttributes([
            'type_of_importance' => 'spam'
        ]);

        // move all not received emails to inbox
        $emailTemplates = MailTemplateModel::model()->findAll(
            " code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }
        // move all not received emails to inbox }
        
        // mark all inbox emails as read
        /*MailBoxModel::model()->updateAll([
                'readed' => 1
            ], 
            'sim_id = :sim_id AND group_id = :group_id', 
            [
                'sim_id'   => $simulation->id,
                'group_id' => 1
            ]
        );*/

        $point = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3313'
        ]);
        
        SimulationService::saveEmailsAnalize($simulation->id);
        
        $result = SimulationMailPoint::model()->findByAttributes([
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
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);
        
        $point_3322 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3324'
        ]);

        $email = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M3');
        $email->readed = 1;
        $email->save();
        $email->refresh();
        $emailTask = MailTasksModel::model()->findByAttributes(['code' => 'M3', 'wr' => 'R']);
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
        
        SimulationService::saveEmailsAnalize($simulation->id);
        
        $result_3322 = SimulationMailPoint::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = SimulationMailPoint::model()->findByAttributes([
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
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);
        
        $point_3322 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3324'
        ]);

        // move all not received emails to inbox
        $emailTemplates = MailTemplateModel::model()->findAll(
            " code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }
        // move all not received emails to inbox }
        
        $email = MailBoxModel::model()->findByAttributes(['code' => 'M3', 'sim_id' => $simulation->id]);
        $emailTask = MailTasksModel::model()->findByAttributes(['code' => 'M3', 'wr' => 'R']);        
        
        SimulationService::saveEmailsAnalize($simulation->id);
        
        $result_3322 = SimulationMailPoint::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = SimulationMailPoint::model()->findByAttributes([
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
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);
        
        $point_3322 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3324'
        ]);

        // move all not received emails to inbox
        /*$emailTemplates = MailTemplateModel::model()->findAll(
            " code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }*/
        // move all not received emails to inbox }
        
        $email = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M3');
        $email->readed = 1;
        $email->save();
        $emailTask = MailTasksModel::model()->findByAttributes(['code' => 'M3', 'wr' => 'W']);        
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
        
        SimulationService::saveEmailsAnalize($simulation->id);
        
        $result_3322 = SimulationMailPoint::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = SimulationMailPoint::model()->findByAttributes([
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
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);
        
        $point_3322 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3324'
        ]);

        // move all not received emails to inbox
        $emailTemplates = MailTemplateModel::model()->findAll(
            " code NOT LIKE 'MY%' AND code NOT LIKE 'MS%' "
        );

        foreach ($emailTemplates as $emailTemplate) {
            MailBoxService::copyMessageFromTemplateByCode($simulation, $emailTemplate->code);
        }
        // move all not received emails to inbox }
        
        $email = MailBoxModel::model()->findByAttributes(['code' => 'M3', 'sim_id' => $simulation->id]);
        $emailTask = MailTasksModel::model()->findByAttributes(['code' => 'M3', 'wr' => 'W']);        
        
        SimulationService::saveEmailsAnalize($simulation->id);
        
        $result_3322 = SimulationMailPoint::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = SimulationMailPoint::model()->findByAttributes([
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
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::TYPE_PROMOTION, $user);
        
        $point = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3313'
        ]);
        
        SimulationService::saveEmailsAnalize($simulation->id);
        
        $result = SimulationMailPoint::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);
        
        $this->assertEquals($result->value, 0);
    }
}

