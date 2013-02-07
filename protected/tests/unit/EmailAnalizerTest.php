<?php

/**
 *
 * @author slavka
 */
class EmailAnalizerTest extends CDbTestCase
{
    /**
     * 
     */
    public function test_3313_AllEmailsReadedCase()
    {
        $this->markTestSkipped();
        
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        $randomSpamMailTemplate = MailTemplateModel::model()->findByAttributes([
            'type_of_importance' => 'spam'
        ]);
        
        // move all not received emails to inbox
        MailBoxModel::model()->updateAll([
                'group_id' => 1
            ], 
            'sim_id = :sim_id AND group_id = :group_id', 
            [
                'sim_id'   => $simulation->id,
                'group_id' => 5
            ]
        );
        
        // mark all inbox emails readed
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
        
        $result = SimulationsMailPointsModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);
        
        $this->assertEquals($result->value, 1);
    }
    
    /**
     * 
     */
    public function test_3313_AllInitialEmailsReadedCase()
    {
        $this->markTestSkipped();
        
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        $randomSpamMailTemplate = MailTemplateModel::model()->findByAttributes([
            'type_of_importance' => 'spam'
        ]);
        
        // mark all inbox emails readed
        MailBoxModel::model()->updateAll([
                'readed' => 1
            ], 
            'sim_id = :sim_id AND group_id = :group_id', 
            [
                'sim_id'   => $simulation->id,
                'group_id' => 1
            ]
        );
        
        // move all not received emails to inbox
        MailBoxModel::model()->updateAll([
                'group_id' => 1
            ], 
            'sim_id = :sim_id AND group_id = :group_id', 
            [
                'sim_id'   => $simulation->id,
                'group_id' => 5
            ]
        );
        
        $point = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3313'
        ]);
        
        SimulationService::saveEmailsAnalize($simulation->id);
        
        $result = SimulationsMailPointsModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);
        
        $this->assertEquals($result->value, 0);
    }
    
    /**
     * 
     */
    public function test_3322_3324_OneFromOneEmailsPlannedRigthCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        $point_3322 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3324'
        ]);
        
        // move all not received emails to inbox
        MailBoxModel::model()->updateAll([
                'group_id' => 1,
             ], 
            'sim_id = :sim_id AND group_id = :group_id AND code IN (\'M3\')', 
            [
                'sim_id'   => $simulation->id,
                'group_id' => 5
            ]
        );
        
        $email = MailBoxModel::model()->findByAttributes(['code' => 'M3', 'sim_id' => $simulation->id]);
        $emailTask = MailTasksModel::model()->findByAttributes(['code' => 'M3', 'wr' => 'R']);        
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
        
        $result_3322 = SimulationsMailPointsModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = SimulationsMailPointsModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3324->id,
        ]);
        
        $this->assertEquals($result_3322->value, $point_3322->scale, '3322 1');
        $this->assertEquals($result_3324->value, 0, '3324 1');
    }
    
    /**
     * 
     */
    public function test_3322_3324_OneFromOneEmailsNotPlannedRigthCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        $point_3322 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3324'
        ]);
        
        // move all not received emails to inbox
        MailBoxModel::model()->updateAll([
                'group_id' => 1,
             ], 
            'sim_id = :sim_id AND group_id = :group_id AND code IN (\'M3\')', 
            [
                'sim_id'   => $simulation->id,
                'group_id' => 5
            ]
        );
        
        $email = MailBoxModel::model()->findByAttributes(['code' => 'M3', 'sim_id' => $simulation->id]);
        $emailTask = MailTasksModel::model()->findByAttributes(['code' => 'M3', 'wr' => 'R']);        
        
        SimulationService::saveEmailsAnalize($simulation->id);
        
        $result_3322 = SimulationsMailPointsModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = SimulationsMailPointsModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3324->id,
        ]);
        
        $this->assertEquals($result_3322->value, 0, '3322 3');
        $this->assertEquals($result_3324->value, 0, '3324 3');
    }
    
    /**
     * 
     */
    public function test_3322_3324_OneFromOneEmailsPlannedWrongCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        $point_3322 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3324'
        ]);
        
        // move all not received emails to inbox
        MailBoxModel::model()->updateAll([
                'group_id' => 1,
             ], 
            'sim_id = :sim_id AND group_id = :group_id AND code IN (\'M3\')', 
            [
                'sim_id'   => $simulation->id,
                'group_id' => 5
            ]
        );
        
        $email = MailBoxModel::model()->findByAttributes(['code' => 'M3', 'sim_id' => $simulation->id]);
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
        
        $result_3322 = SimulationsMailPointsModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = SimulationsMailPointsModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3324->id,
        ]);
        
        $this->assertEquals($result_3322->value, 0, '3322 1');
        $this->assertEquals($result_3324->value, $point_3324->scale, '3324 2');
    }
    
    /**
     * 
     */
    public function test_3322_3324_OneFromOneEmailsNotPlannedWrongCase()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        $point_3322 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3322'
        ]);
        
        $point_3324 = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3324'
        ]);
        
        // move all not received emails to inbox
        MailBoxModel::model()->updateAll([
                'group_id' => 1,
             ], 
            'sim_id = :sim_id AND group_id = :group_id AND code IN (\'M3\')', 
            [
                'sim_id'   => $simulation->id,
                'group_id' => 5
            ]
        );
        
        $email = MailBoxModel::model()->findByAttributes(['code' => 'M3', 'sim_id' => $simulation->id]);
        $emailTask = MailTasksModel::model()->findByAttributes(['code' => 'M3', 'wr' => 'W']);        
        
        SimulationService::saveEmailsAnalize($simulation->id);
        
        $result_3322 = SimulationsMailPointsModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3322->id,
        ]);
        
        $result_3324 = SimulationsMailPointsModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point_3324->id,
        ]);
        
        $this->assertEquals($result_3322->value, 0, '3322 4');
        $this->assertEquals($result_3324->value, 0, '3324 4');
    }
    
    /**
     * 
     */
    public function test_3313_NoEmailsReadedCase()
    {
        $this->markTestSkipped();
        
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        $point = CharactersPointsTitles::model()->findByAttributes([
            'code' => '3313'
        ]);
        
        SimulationService::saveEmailsAnalize($simulation->id);
        
        $result = SimulationsMailPointsModel::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'point_id' => $point->id,
        ]);
        
        $this->assertEquals($result->value, 0);
    }
}

