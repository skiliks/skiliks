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
    public function test_3313_NoEmailsReadedCase()
    {
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

