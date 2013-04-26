<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 4/26/13
 * Time: 11:40 AM
 * To change this template use File | Settings | File Templates.
 */
class Assessment_Goals_Areas_Overals extends CDbTestCase
{
    use UnitLoggingTrait;

    public function testAssessment_Goals_Areas_Overals_case1()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
//        $invite = new Invite();
//        $invite->scenario = new Scenario();
//        $invite->receiverUser = $user;
//        $invite->scenario->slug = Scenario::TYPE_FULL;
//        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);
        $simulation = Simulation::model()->findByPk(1501);

        AssessmentAggregated::model()->deleteAllByAttributes(['sim_id' => $simulation->id]);
        AssessmentOverall::model()->deleteAllByAttributes(['sim_id' => $simulation->id]);
        StressPoint::model()->deleteAllByAttributes(['sim_id' => $simulation->id]);
        SimulationLearningGoal::model()->deleteAllByAttributes(['sim_id' => $simulation->id]);
        SimulationLearningArea::model()->deleteAllByAttributes(['sim_id' => $simulation->id]);

        $this->addAssessmentAggregated($simulation, '214d0');
        $this->addAssessmentAggregated($simulation, '214d1');
        $this->addAssessmentAggregated($simulation, '214d2');
        $this->addAssessmentAggregated($simulation, '214d3');
        $this->addAssessmentAggregated($simulation, '214d4');

        $this->addAssessmentAggregated($simulation, '341b5');
        $this->addAssessmentAggregated($simulation, '341b7');
        $this->addAssessmentAggregated($simulation, '4121'); // 8
        $this->addAssessmentAggregated($simulation, '4124'); // 8
        $this->addAssessmentAggregated($simulation, '3216');
        $this->addAssessmentAggregated($simulation, '4122'); // 8
        $this->addAssessmentAggregated($simulation, '341b1');
        $this->addAssessmentAggregated($simulation, '351b3');
        $this->addAssessmentAggregated($simulation, '4125'); // 8
        $this->addAssessmentAggregated($simulation, '4141'); // 8
        $this->addAssessmentAggregated($simulation, '4143'); // 8
        $this->addAssessmentAggregated($simulation, '4153'); // 8
        $this->addAssessmentAggregated($simulation, '4127'); // 8
        $this->addAssessmentAggregated($simulation, '3214');
        $this->addAssessmentAggregated($simulation, '351a2');
        $this->addAssessmentAggregated($simulation, '1122');
        $this->addAssessmentAggregated($simulation, '1232'); // 1
        $this->addAssessmentAggregated($simulation, '3218'); // 1
        $this->addAssessmentAggregated($simulation, '351b2');
        $this->addAssessmentAggregated($simulation, '351b1');
        $this->addAssessmentAggregated($simulation, '351c1');
        $this->addAssessmentAggregated($simulation, '351c3');
        $this->addAssessmentAggregated($simulation, '3322');
        $this->addAssessmentAggregated($simulation, '3323');
        $this->addAssessmentAggregated($simulation, '3313');
        $this->addAssessmentAggregated($simulation, '3333');
        $this->addAssessmentAggregated($simulation, '3326');
        $this->addAssessmentAggregated($simulation, '3311');
        $this->addAssessmentAggregated($simulation, '3332');
        $this->addAssessmentAggregated($simulation, '214a1');
        $this->addAssessmentAggregated($simulation, '214a3');
        $this->addAssessmentAggregated($simulation, '214a4');
        $this->addAssessmentAggregated($simulation, '214a5');
        $this->addAssessmentAggregated($simulation, '214b0');
        $this->addAssessmentAggregated($simulation, '214b1');
        $this->addAssessmentAggregated($simulation, '214b2');
        $this->addAssessmentAggregated($simulation, '214b3');
        $this->addAssessmentAggregated($simulation, '214b4');
        $this->addAssessmentAggregated($simulation, '214b9');

//        $this->addAssessmentAggregated($simulation, '3324');
//        $this->addAssessmentAggregated($simulation, '3325');
//        $this->addAssessmentAggregated($simulation, '214a8');
//        $this->addAssessmentAggregated($simulation, '214b5');
//        $this->addAssessmentAggregated($simulation, '214b6');
//        $this->addAssessmentAggregated($simulation, '214b8');
        $this->addAssessmentAggregated($simulation, '8311', 100);
        $this->addAssessmentAggregated($simulation, '8351', 100);
        $this->addAssessmentAggregated($simulation, '8331', 100);
        $this->addAssessmentAggregated($simulation, '8381', 100);
        $this->addAssessmentAggregated($simulation, '8211', 100); // Выполняет свои обещания (% выполненных обещаний)
        $this->addAssessmentAggregated($simulation, '8212', 100); // Несёт ответственность за свои поступки
        $this->addAssessmentAggregated($simulation, '8213', 100); // Несёт ответственность за своих подчинённых
        $this->addAssessmentAggregated($simulation, '8341', 100);
        $this->addAssessmentAggregated($simulation, '8371', 100);
        $this->addAssessmentAggregated($simulation, '7211', 100);
        $this->addAssessmentAggregated($simulation, '7141', 150); // Stress resistance

        // -------------------------------

        $learningGoalAnalyzer = new LearningGoalAnalyzer($simulation);
        $learningGoalAnalyzer->run();

        $learning_area = new LearningAreaAnalyzer($simulation);
        $learning_area->run();

        $evaluation = new Evaluation($simulation);
        $evaluation->run();

        $goals   = SimulationLearningGoal::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $areas   = SimulationLearningArea::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $overall = AssessmentOverall::model()->findAllByAttributes(['sim_id' => $simulation->id]);

        echo 'Sim id = '.$simulation->id."\n\n";
        echo "\n Goals: \n";

        foreach ($goals as $listItem) {
            echo sprintf(
                "%s %s : %s \n",
                $listItem->learningGoal->code,
                $listItem->learningGoal->title,
                round($listItem->value, 2)
            );
        }

        echo "\n Areas: \n";
        foreach ($areas as $listItem) {
            echo sprintf(
                "%s %s : %s \n",
                $listItem->learningArea->code,
                $listItem->learningArea->title,
                round($listItem->value, 2)
            );
        }

        echo "\n Overals: \n";
        foreach ($overall as $listItem) {
            echo sprintf(
                "%s : %s \n",
                $listItem->assessment_category_code,
                round($listItem->value, 2)
            );
        }
    }

    // -----------------------------------------------------

    private function addStressPoint(Simulation $simulation, $code )
    {
        if (is_integer($code)) {
            $stresRule = $simulation->game_type->getStressRule(['code' => $code]);
        } elseif ($code instanceof HeroBehaviour) {
            $stresRule = $code;
        }

        if (null == $stresRule) {
            return false;
        }

        $item = new StressPoint();
        $item->sim_id         = $simulation->id;
        $item->stress_rule_id = $stresRule->id;
        $item->save();
    }

    private function addAssessmentAggregated(Simulation $simulation, $code, $value = null, $k = 1 )
    {
        if (is_string($code)) {
            $behaviour = $simulation->game_type->getHeroBehaviour(['code' => $code]);
        } elseif ($code instanceof HeroBehaviour) {
            $behaviour = $code;
        }

        if (null == $behaviour) {
            return false;
        }
        $value = (null === $value) ? $behaviour->scale : $value;

        $item = new AssessmentAggregated();
        $item->sim_id      = $simulation->id;
        $item->point_id    = $behaviour->id;
        $item->value       = $k * $value;
        $item->fixed_value = $k * $value;
        $item->save();

        return $item;
    }

    private function addAssessmentPoints(Simulation $simulation, $code, $k = 1 )
    {
        if (is_string($code)) {
            $behaviour = $simulation->game_type->getHeroBehaviour(['code' => $code]);
        } elseif ($code instanceof HeroBehaviour) {
            $behaviour = $code;
        }

        if (null == $behaviour) {
            return false;
        }

        $item = new AssessmentPoint();
        $item->sim_id   = $simulation->id;
        $item->point_id = $behaviour->id;
        $item->value    = $k * $behaviour->scale;
        $item->save(false);
    }

    private function addAssessmentCalculation(Simulation $simulation, $code, $k = 1 )
    {
        if (is_string($code)) {
            $behaviour = $simulation->game_type->getHeroBehaviour(['code' => $code]);
        } elseif ($code instanceof HeroBehaviour) {
            $behaviour = $code;
        }

        if (null == $behaviour) {
            return false;
        }

        $item = new AssessmentCalculation();
        $item->sim_id   = $simulation->id;
        $item->point_id = $behaviour->id;
        $item->value    = $k * $behaviour->scale;
        $item->save();
    }
}