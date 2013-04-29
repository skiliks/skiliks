<?php

class AggregationTest extends CDbTestCase
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
}