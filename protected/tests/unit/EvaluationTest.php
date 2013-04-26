<?php

class EvaluationTest extends PHPUnit_Framework_TestCase {


    public function testBadEvaluation() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // init data {
        $lgs = $simulation->game_type->getLearningGoals();

        $goals = [];
        $i = 1;
        foreach($lgs as $lg) {
            $goals[$i] = $lg;
            $i++;
            if (3 < $i) {
                break;
            }
        }

        $simLearnGoal = new SimulationLearningGoal();
        $simLearnGoal->sim_id = $simulation->id;
        $simLearnGoal->learning_goal_id = $goals[1]->id;
        $simLearnGoal->percent = 20;
        $simLearnGoal->value = 20;
        $simLearnGoal->problem = 30;
        $simLearnGoal->save();
        // init data }

        $evaluation = new Evaluation($simulation);
        $evaluation->checkManagerialSkills();

        $sim = Simulation::model()->findByAttributes(['id'=>$simulation->id]);
        $this->assertEquals(10, $sim->getCategoryAssessment(AssessmentCategory::MANAGEMENT_SKILLS));

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

        // ---------------------------

        SimulationService::calculatePerformanceRate($simulation);

        $assessmentOverall = new AssessmentOverall();
        $assessmentOverall->sim_id = $simulation->id;
        $assessmentOverall->assessment_category_code = AssessmentCategory::TIME_EFFECTIVENESS;
        $assessmentOverall->value = 30;
        $assessmentOverall->save();

        $evaluation = new Evaluation($simulation);
        $evaluation->checkManagerialProductivity();
        $sim = Simulation::model()->findByAttributes(['id'=>$simulation->id]);
        $this->assertEquals(7, $sim->getCategoryAssessment(AssessmentCategory::PRODUCTIVITY));

        $evaluation = new Evaluation($simulation);
        $evaluation->checkOverallManagerRating();
        $sim = Simulation::model()->findByAttributes(['id'=>$simulation->id]);
        $this->assertEquals(13, $sim->getCategoryAssessment(AssessmentCategory::OVERALL));

    }

}
