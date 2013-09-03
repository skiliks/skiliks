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
        $learningArea = $scenario->getLearningArea(['code' => '3']);

        foreach ($behaviours as $behaviour) {
            $behaviourValue = new AssessmentAggregated();
            $behaviourValue->sim_id = $simulation->id;
            $behaviourValue->point_id = $behaviour->id;
            $behaviourValue->value = $behaviour->scale;

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

        $this->assertEquals(17.142857, $learningAreaValue->value, 'value Area');
    }

    public function testActivityActionAgregationSpecificCase()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        // log1, 1st priority doc {
        $doc_d2 = $simulation->game_type->getDocumentTemplate(['code' => 'D2']);

        $activity_d1 = $simulation->game_type->getActivity(['code' => 'T2']);

        $activity_action_d1 = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_d1->id,
            'document_id' => $doc_d2->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action = 'T2';
        $log->activity_action_id = $activity_action_d1->id;
        $log->activityAction = $activity_action_d1;
        $log->category = $activity_d1->category->code;
        $log->start_time = '09:15:00';
        $log->end_time = '10:00:00';
        $log->duration = '00:45:00';
        $log->save();
        // log1, 1st priority doc }

        // log2, non priority doc {
        $doc_d14 = $simulation->game_type->getDocumentTemplate(['code' => 'D14']);

        $activity_d14 = $simulation->game_type->getActivity(['code' => 'AD14']);

        $activity_action_d14 = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_d14->id,
            'document_id' => $doc_d14->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_DOCUMENTS;
        $log->leg_action = 'D14';
        $log->activity_action_id = $activity_action_d14->id;
        $log->activityAction = $activity_action_d14;
        $log->category = $activity_d14->category->code;
        $log->start_time = '10:00:00';
        $log->end_time = '10:10:00';
        $log->duration = '00:10:00';
        $log->save();
        // log2, non priority doc }

        // log3, 1st priority phone call {
        $replica_RS1 = $simulation->game_type->getReplica(['excel_id' => '524']);

        $activity_RS1 = $simulation->game_type->getActivity(['code' => 'T3.1']);

        $activity_action_replica_RS1 = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_RS1->id,
            'dialog_id' => $replica_RS1->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_MANUAL_DIAL;
        $log->leg_action = 'T3.1';
        $log->activity_action_id = $activity_action_replica_RS1->id;
        $log->activityAction = $activity_action_replica_RS1;
        $log->category = $activity_RS1->category->code;
        $log->start_time = '10:10:00';
        $log->end_time = '10:20:00';
        $log->duration = '00:10:00';
        $log->save();
        // log3, 1st priority phone call }

        // log4, non priority phone call {
        $replica_RS1 = $simulation->game_type->getReplica(['excel_id' => '624']);

        $activity_RS1 = $simulation->game_type->getActivity(['code' => 'ARS1']);

        $activity_action_replica_RS1 = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_RS1->id,
            'dialog_id' => $replica_RS1->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_MANUAL_DIAL;
        $log->leg_action = 'ARS1';
        $log->activity_action_id = $activity_action_replica_RS1->id;
        $log->activityAction = $activity_action_replica_RS1;
        $log->category = $activity_RS1->category->code;
        $log->start_time = '10:20:00';
        $log->end_time = '10:30:00';
        $log->duration = '00:10:00';
        $log->save();
        // log4, non priority phone call }

        // log5, 1st priority planing {
        $activity_T1 = $simulation->game_type->getActivity(['code' => 'T1.1']);

        $activity_action_replica_T1 = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_T1->id,
            'dialog_id'   => null,
            'mail_id'     => null,
            'document_id' => null,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_MANUAL_DIAL;
        $log->leg_action = 'plan';
        $log->activity_action_id = $activity_action_replica_T1->id;
        $log->activityAction = $activity_action_replica_T1;
        $log->category = $activity_T1->category->code;
        $log->start_time = '10:40:00';
        $log->end_time = '10:50:00';
        $log->duration = '00:10:00';
        $log->save();
        // log5, 1st priority planing }

        // log6, non priority planing {
        // такого нет
        // log6, non priority planing }

        // log7, 1st priority mail {
        $template_M2 = $simulation->game_type->getMailTemplate(['code' => 'M2']);

        $activity_M2 = $simulation->game_type->getActivity(['code' => 'TM2']);

        $activity_action_M2 = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_M2->id,
            'mail_id' => $template_M2->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_MANUAL_DIAL;
        $log->leg_action = 'AM2';
        $log->activity_action_id = $activity_action_M2->id;
        $log->activityAction = $activity_action_M2;
        $log->category = $activity_M2->category->code;
        $log->start_time = '10:50:00';
        $log->end_time = '10:00:00';
        $log->duration = '00:10:00';
        $log->save();
        // log7, 1st priority mail }

        // log8, non priority mail {
        $template_MY2 = $simulation->game_type->getMailTemplate(['code' => 'MY2']);

        $activity_MY2 = $simulation->game_type->getActivity(['code' => 'AMY2']);

        $activity_action_MY2 = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_MY2->id,
            'mail_id' => $template_MY2->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_INBOX;
        $log->leg_action = 'AMY2';
        $log->activity_action_id = $activity_action_MY2->id;
        $log->activityAction = $activity_action_MY2;
        $log->category = $activity_MY2->category->code;
        $log->start_time = '10:00:00';
        $log->end_time = '11:10:00';
        $log->duration = '00:10:00';
        $log->save();
        // log8, non priority mail }

        // log9, 1st priority meeting {
        $replica_S1_2 = $simulation->game_type->getReplica(['excel_id' => '19']);

        $activity_S1_2 = $simulation->game_type->getActivity(['code' => 'AE1']);

        $activity_action_replica_S1_2 = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_S1_2->id,
            'dialog_id' => $replica_S1_2->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action = 'AE1';
        $log->activity_action_id = $activity_action_replica_S1_2->id;
        $log->activityAction = $activity_action_replica_S1_2;
        $log->category = $activity_S1_2->category->code;
        $log->start_time = '11:20:00';
        $log->end_time = '11:30:00';
        $log->duration = '00:10:00';
        $log->save();
        // log9, 1st priority meeting }

        // log10, non priority meeting {
        $replica_AE3 = $simulation->game_type->getReplica(['excel_id' => '239']);

        $activity_AE3 = $simulation->game_type->getActivity(['code' => 'AE3']);

        $activity_action_replica_AE3 = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_AE3->id,
            'dialog_id' => $replica_AE3->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_WINDOW;
        $log->leg_action = 'AE3';
        $log->activity_action_id = $activity_action_replica_AE3->id;
        $log->activityAction = $activity_action_replica_AE3;
        $log->category = $activity_AE3->category->code;
        $log->start_time = '11:30:00';
        $log->end_time = '11:40:00';
        $log->duration = '00:10:00';
        $log->save();
        // log10, non priority meeting }

        // log11, A_wait {
        $window = Window::model()->findByAttributes(['subtype' => 'main screen']);

        $activity_A_wait = $simulation->game_type->getActivity(['code' => 'A_wait']);

        $activity_action_A_wait = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_A_wait->id,
            'window_id'   => $window->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_SYSTEM_DIAL;
        $log->leg_action = null;
        $log->activity_action_id = $activity_action_A_wait->id;
        $log->activityAction = $activity_action_A_wait;
        $log->category = $activity_A_wait->category->code;
        $log->start_time = '11:40:00';
        $log->end_time = '11:50:00';
        $log->duration = '00:10:00';
        $log->save();
        // log11, A_wait }

        // log12, A_wait {
        $window = Window::model()->findByAttributes(['subtype' => 'phone talk']);

        $activity_A_wrong_call = $simulation->game_type->getActivity(['code' => 'A_wrong_call']);

        $activity_action_A_wrong_call = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_A_wrong_call->id,
            'leg_type'    => ActivityAction::LEG_TYPE_MANUAL_DIAL
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_MANUAL_DIAL;
        $log->leg_action = null;
        $log->activity_action_id = $activity_action_A_wrong_call->id;
        $log->activityAction = $activity_action_A_wrong_call;
        $log->category = $activity_A_wrong_call->category->code;
        $log->start_time = '11:50:00';
        $log->end_time = '10:00:00';
        $log->duration = '00:10:00';
        $log->save();
        // log12, A_wait }

        // log13, A_wait {
        $activity_A_not_sent = $simulation->game_type->getActivity(['code' => 'A_not_sent']);

        $activity_action_A_not_sent = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_A_not_sent->id,
            'leg_type'    => ActivityAction::LEG_TYPE_OUTBOX,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_OUTBOX;
        $log->leg_action = null;
        $log->activity_action_id = $activity_action_A_not_sent->id;
        $log->activityAction = $activity_action_A_not_sent;
        $log->category = $activity_A_not_sent->category->code;
        $log->start_time = '12:00:00';
        $log->end_time = '12:10:00';
        $log->duration = '00:10:00';
        $log->save();
        // log13, A_wait }

        // log13, A_wait {
        $activity_A_incorrect_sent = $simulation->game_type->getActivity(['code' => 'A_incorrect_sent']);

        $activity_action_A_incorrect_sent = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_A_incorrect_sent->id,
            'leg_type'    => ActivityAction::LEG_TYPE_OUTBOX,
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_OUTBOX;
        $log->leg_action = null;
        $log->activity_action_id = $activity_action_A_incorrect_sent->id;
        $log->activityAction = $activity_action_A_incorrect_sent;
        $log->category = $activity_A_incorrect_sent->category->code;
        $log->start_time = '18:30:00';
        $log->end_time = '18:40:00';
        $log->duration = '00:10:00';
        $log->save();

        $planAnalyzer = new PlanAnalyzer($simulation);

        foreach($planAnalyzer->simulation->log_activity_actions_aggregated as $logItem) {
            $this->assertNotNull($logItem->keep_last_category_after_60_sec, "Keep last category after 60 sec is NULL");
        }


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

        $this->assertGreaterThan(0, count($behaviours), 'Too few behaviours!');

        foreach ($simulation->assessment_aggregated as $mark) {
            if ($mark->point->isPositive()) {
                $this->assertEquals($mark->value, $mark->point->scale/2, $mark->point->code);
            } elseif ($mark->point->isNegative()) {
                $this->assertEquals($mark->value, $mark->point->scale*2, $mark->point->code);
            } else {
                throw new InvalidArgumentException('Matrix behaviour produce assessment on personal scale!', 10);
            }
        }
    }
}