<?php

class EvaluationTest extends PHPUnit_Framework_TestCase {


    public function testBadEvaluation() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user, Simulation::TYPE_FULL);

        $asses = new AssessmentAggregated();
        $asses->point_id = 1;
        $asses->sim_id = $simulation->id;
        $asses->fixed_value = 34.50;
        $asses->save();

        $asses = new AssessmentAggregated();
        $asses->point_id = 2;
        $asses->sim_id = $simulation->id;
        $asses->fixed_value = 26.50;
        $asses->save();

        $asses = new AssessmentAggregated();
        $asses->point_id = 3;
        $asses->sim_id = $simulation->id;
        $asses->fixed_value = -11;
        $asses->save();

        $evaluation = new Evaluation($simulation);
        $evaluation->checkManagerialSkills();

        $sim = Simulation::model()->findByAttributes(['id'=>$simulation->id]);
        $this->assertEquals('50.00', $sim->managerial_skills);

        $asses = new PerformancePoint();
        $asses->performance_rule_id = $simulation->game_type->getPerformanceRule(['code' => 1])->getPrimaryKey();
        $asses->sim_id = $simulation->id;
        $asses->save();

        $asses = new PerformancePoint();
        $asses->performance_rule_id = $simulation->game_type->getPerformanceRule(['code' => 2])->getPrimaryKey();
        $asses->sim_id = $simulation->id;
        $asses->save();

        $asses = new PerformancePoint();
        $asses->performance_rule_id = $simulation->game_type->getPerformanceRule(['code' => 3])->getPrimaryKey();
        $asses->sim_id = $simulation->id;
        $asses->save();

        $evaluation = new Evaluation($simulation);
        $evaluation->checkManagerialProductivity();

        $sim = Simulation::model()->findByAttributes(['id'=>$simulation->id]);
        $this->assertEquals('13.00', $sim->managerial_productivity); // 3.00

        $evaluation = new Evaluation($simulation);
        $evaluation->checkOverallManagerRating();
        $sim = Simulation::model()->findByAttributes(['id'=>$simulation->id]);
        $this->assertEquals('28.90', $sim->overall_manager_rating); // 25.90

    }

}
