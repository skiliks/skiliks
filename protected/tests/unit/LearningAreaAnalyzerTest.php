<?php

class LearningAreaAnalyzerTest extends PHPUnit_Framework_TestCase {

    public function addValueByCode($simulation, $code, $value) {

        /* @var $simulation Simulation */
        /* @var $point HeroBehaviour */
        /* @var $assessment AssessmentAggregated */
        $point = $simulation->game_type->getHeroBehaviour(['code'=>$code]);
        $assessment = AssessmentAggregated::model()->findByAttributes(['sim_id'=>$simulation->id, 'point_id'=> $point->id]);
        if(null === $assessment){
            $assessment = new AssessmentAggregated();
            $assessment->point_id = $point->id;
            $assessment->sim_id = $simulation->id;
        }
            $assessment->value = $value;
            $assessment->save();

    }

    public function testAdoptionOfDecisions() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $this->addValueByCode($simulation, 8311, 1);
        //$this->addValueByCode($simulation, 8321, 0);
        //$this->addValueByCode($simulation, 8331, 0);
        //$this->addValueByCode($simulation, 8341, 0);
        $this->addValueByCode($simulation, 8351, 1);
        //$this->addValueByCode($simulation, 8361, 0);

        $learn = new LearningAreaAnalyzer($simulation);
        $learn->adoptionOfDecisions();

        $code = $simulation->game_type->getLearningArea(['code'=>13]);
        $assessment = SimulationLearningArea::model()->findByAttributes(['sim_id' => $simulation->id, 'learning_area_id' => $code->id]);
        /* @var $assessment SimulationLearningArea */
        $this->assertEquals('2.410000', $assessment->value);
    }

    public function testAdoptionOfDecisionsBad() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $learn = new LearningAreaAnalyzer($simulation);
        $learn->adoptionOfDecisions();

        $code = $simulation->game_type->getLearningArea(['code'=>13]);
        $assessment = SimulationLearningArea::model()->findByAttributes(['sim_id' => $simulation->id, 'learning_area_id' => $code->id]);
        /* @var $assessment SimulationLearningArea */
        $this->assertEquals('0.00', $assessment->value);
    }

}
