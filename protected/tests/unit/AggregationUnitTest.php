<?php

class AggregationUnitTest extends CDbTestCase
{
    public function testCommunicationAggregationSpecificCase()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        $scenario = $simulation->game_type;

        $behaviours   = $scenario->getHeroBehavours(['code' => ['3214', '3216', '3218']]);
        $learningGoal = $scenario->getLearningGoal(['code' => '321']);
        $learningArea = $scenario->getLearningArea(['code' => '4']);

        foreach ($behaviours as $behaviour) {
            $behaviourValue = new AssessmentAggregated();
            $behaviourValue->sim_id = $simulation->id;
            $behaviourValue->point_id = $behaviour->id;
            $behaviourValue->value = $behaviour->scale;
            $behaviourValue->fixed_value = $behaviour->scale;

            $behaviourValue->save();
        }

        $analyzer = new LearningGoalAnalyzer($simulation);
        $analyzer->run();

        $analyzer = new LearningAreaAnalyzer($simulation);
        $analyzer->run();

        $learningGoalValue = SimulationLearningGoal::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'learning_goal_id' => $learningGoal->id
        ]);

        $learningAreaValue = SimulationLearningArea::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'learning_area_id' => $learningArea->id
        ]);

        $this->assertEquals(6  , $learningGoalValue->value, 'value Goal');
        $this->assertEquals(100, $learningGoalValue->percent, 'percent Goal');

        $this->assertEquals(100, $learningAreaValue->value, 'value Area');
    }

    /**
     * Проверяет что за очки в AssessmentPoint пользователь получит оценку в AssessmentAggregated
     *
     * @throws InvalidArgumentException
     */
    public function testMatrixPointsAggregation()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $behaviours = $simulation->game_type->getHeroBehavours([]);

        foreach ($behaviours as $behaviour) {
            if ($behaviour->isPositive()) {
                $point1           = new AssessmentPoint();
                $point1->sim_id   = $simulation->id;
                $point1->point_id = $behaviour->id;
                $point1->value    = 1;
                $point1->save();

                $point0           = new AssessmentPoint();
                $point0->sim_id   = $simulation->id;
                $point0->point_id = $behaviour->id;
                $point0->value    = 0;
                $point0->save();
            } elseif ($behaviour->isNegative()) {
                $point1           = new AssessmentPoint();
                $point1->sim_id   = $simulation->id;
                $point1->point_id = $behaviour->id;
                $point1->value    = 1;
                $point1->save();

                $point2           = new AssessmentPoint();
                $point2->sim_id   = $simulation->id;
                $point2->point_id = $behaviour->id;
                $point2->value    = 1;
                $point2->save();
            }
        }

        SimulationService::saveAggregatedPoints($simulation->id);
        SimulationService::applyReductionFactors($simulation);

        $this->assertGreaterThan(0, count($behaviours), 'Too few behaviours!');

        foreach ($simulation->assessment_aggregated as $mark) {
            if ($mark->point->isPositive()) {
                $this->assertEquals($mark->fixed_value, $mark->point->scale/2, $mark->point->code);
            } elseif ($mark->point->isNegative()) {
                $this->assertEquals($mark->fixed_value, $mark->point->scale*2, $mark->point->code);
            } else {
                throw new InvalidArgumentException('Matrix behaviour produce assessment on personal scale!', 10);
            }
        }
    }
}