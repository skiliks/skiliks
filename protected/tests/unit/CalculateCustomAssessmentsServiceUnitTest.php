<?php

class CalculateCustomAssessmentsServiceUnitTest extends PHPUnit_Framework_TestCase {

    use UnitTestBaseTrait;

    public function test_check_3312_341a8()
    {
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $behaviour_3312 = HeroBehaviour::model()->findByAttributes(['code'=>'3312']);
        $behaviour_341a8 = HeroBehaviour::model()->findByAttributes(['code'=>'341a8']);

        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_FALSE, LogIncomingCallSoundSwitcher::INCOMING_MAIL, "10:00:00");
        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_TRUE, LogIncomingCallSoundSwitcher::INCOMING_MAIL, "10:10:00");
        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_FALSE, LogIncomingCallSoundSwitcher::INCOMING_MAIL, "10:20:00");
        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_TRUE, LogIncomingCallSoundSwitcher::INCOMING_MAIL, "10:30:00");

        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_FALSE, LogIncomingCallSoundSwitcher::INCOMING_CALL, "11:00:00");
        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_TRUE, LogIncomingCallSoundSwitcher::INCOMING_CALL, "11:10:00");
        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_FALSE, LogIncomingCallSoundSwitcher::INCOMING_CALL, "11:20:00");
        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_TRUE, LogIncomingCallSoundSwitcher::INCOMING_CALL, "11:30:00");


        $service = new CalculateCustomAssessmentsService($simulation);
        $service->run();

        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour_3312->id]);
        $this->assertEquals('0.00', $point->value);

        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour_341a8->id]);
        $this->assertEquals('0.00', $point->value);

        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_FALSE, LogIncomingCallSoundSwitcher::INCOMING_MAIL, "10:40:00");
        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_TRUE, LogIncomingCallSoundSwitcher::INCOMING_MAIL, "10:55:00");

        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_FALSE, LogIncomingCallSoundSwitcher::INCOMING_CALL, "11:40:00");
        $this->addSwitchSound($simulation, LogIncomingCallSoundSwitcher::IS_PLAY_TRUE, LogIncomingCallSoundSwitcher::INCOMING_CALL, "11:55:00");

        $service = new CalculateCustomAssessmentsService($simulation);
        $service->run();

        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour_3312->id]);
        $this->assertEquals($behaviour_3312->scale, $point->value);

        $point = AssessmentCalculation::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=>$behaviour_341a8->id]);
        $this->assertEquals($behaviour_341a8->scale, $point->value);
    }

    public function addSwitchSound(Simulation $simulation, $is_play, $sound_alias, $time) {

        $log = new LogIncomingCallSoundSwitcher();
        $log->sim_id = $simulation->id;
        $log->is_play = $is_play;
        $log->sound_alias = $sound_alias;
        $log->game_time = $time;
        if(false === $log->save()){
            throw new LogicException("No valid data");
        }
    }


}
