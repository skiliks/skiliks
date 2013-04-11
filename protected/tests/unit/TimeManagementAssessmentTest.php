<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 4/10/13
 * Time: 8:15 PM
 * To change this template use File | Settings | File Templates.
 */

class TimeManagementAssessmentTest extends CDbTestCase
{
    use UnitLoggingTrait;

    /**
     * Каждого типа лога по 1 штуке
     */
    public function testimeManagementAssessment_case1()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        // log1, 1st priority doc {
        $doc_d1 = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);

        $activity_d1 = $simulation->game_type->getActivity(['code' => 'TRS6']);

        $activity_action_d1 = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_d1->id,
            'document_id' => $doc_d1->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_DOCUMENTS;
        $log->leg_action = 'D1';
        $log->activity_action_id = $activity_action_d1->id;
        $log->activityAction = $activity_action_d1;
        $log->category = $activity_d1->category->code;
        $log->start_time = '09:45:00';
        $log->end_time = '10:00:00';
        $log->duration = '00:15:00';
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
        // log13, A_wait }

        $tma = new TimeManagementAnalyzer($simulation);
        $tma->calculateAndSaveAssessments();

        $assessments = TimeManagementAggregated::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        $values = [];
        foreach ($assessments as $assessment) {
            $values[$assessment->slug] = $assessment->value;
        }

        $this->assertEquals(
            41.00, // %
            $values['time_spend_for_1st_priority_activities'],
            'time_spend_for_1st_priority_activities'
        );

        $this->assertEquals(
            30.00, // %
            $values['time_spend_for_non_priority_activities'],
            'time_spend_for_non_priority_activities'
        );

        $this->assertEquals(
            29.00, // %
            $values['time_spend_for_inactivity'],
            'time_spend_for_inactivity'
        );

        $this->assertEquals(
            27.00, // %
            $values['1st_priority_documents'],
            '1st_priority_documents'
        );

        $this->assertEquals(
            18.00, // %
            $values['1st_priority_meetings'],
            '1st_priority_meetings'
        );

        $this->assertEquals(
            18.00, // %
            $values['1st_priority_phone_calls'],
            '1st_priority_phone_calls '
        );

        $this->assertEquals(
            18.00, // %
            $values['1st_priority_mail'],
            '1st_priority_mail'
        );

        $this->assertEquals(
            19.00, // %
            $values['1st_priority_planning'],
            '1st_priority_planning'
        );

        $this->assertEquals(
            25.00, // %
            $values['non_priority_documents'],
            'non_priority_documents'
        );

        $this->assertEquals(
            25.00, // %
            $values['non_priority_meetings'],
            'non_priority_meetings'
        );

        $this->assertEquals(
            25.00, // %
            $values['non_priority_phone_calls'],
            'non_priority_phone_calls'
        );

        $this->assertEquals(
            25.00, // %
            $values['non_priority_mail'],
            'non_priority_mail'
        );

        $this->assertEquals(
            0.00, // %
            $values['non_priority_planning'],
            'non_priority_planning'
        );

        $this->assertEquals(
            40.00, // minutes
            $values['workday_overhead_duration'],
            'workday_overhead_duration'
        );

        $this->assertEquals(
            24.60, // 41.00 * 0.6
            $values['efficiency'],
            'efficiency'
        );
    }

    /**
     * Пользователь ничего не сделал
     */
    public function testimeManagementAssessment_case2()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $tma = new TimeManagementAnalyzer($simulation);
        $tma->calculateAndSaveAssessments();

        $assessments = TimeManagementAggregated::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        $values = [];
        foreach ($assessments as $assessment) {
            $values[$assessment->slug] = $assessment->value;
        }

        $this->assertEquals(
            0, // %
            $values['time_spend_for_1st_priority_activities'],
            'time_spend_for_1st_priority_activities'
        );

        $this->assertEquals(
            0, // %
            $values['time_spend_for_non_priority_activities'],
            'time_spend_for_non_priority_activities'
        );

        $this->assertEquals(
            100, // %
            $values['time_spend_for_inactivity'],
            'time_spend_for_inactivity'
        );

        $this->assertEquals(
            0, // %
            $values['1st_priority_documents'],
            '1st_priority_documents'
        );

        $this->assertEquals(
            0, // %
            $values['1st_priority_meetings'],
            '1st_priority_meetings'
        );

        $this->assertEquals(
            0, // %
            $values['1st_priority_phone_calls'],
            '1st_priority_phone_calls '
        );

        $this->assertEquals(
            0, // %
            $values['1st_priority_mail'],
            '1st_priority_mail'
        );

        $this->assertEquals(
            0, // %
            $values['1st_priority_planning'],
            '1st_priority_planning'
        );

        $this->assertEquals(
            0, // %
            $values['non_priority_documents'],
            'non_priority_documents'
        );

        $this->assertEquals(
            0, // %
            $values['non_priority_meetings'],
            'non_priority_meetings'
        );

        $this->assertEquals(
            0, // %
            $values['non_priority_phone_calls'],
            'non_priority_phone_calls'
        );

        $this->assertEquals(
            0, // %
            $values['non_priority_mail'],
            'non_priority_mail'
        );

        $this->assertEquals(
            0.00, // %
            $values['non_priority_planning'],
            'non_priority_planning'
        );

        $this->assertEquals(
            0, // minutes
            $values['workday_overhead_duration'],
            'workday_overhead_duration'
        );

        $this->assertEquals(
            0, // 0.00 * 1.0
            $values['efficiency'],
            'efficiency'
        );
    }

    /**
     * For debug
     */
//    public function testimeManagementAssessment_case3()
//    {
//        // init simulation
//        $simulation = Simulation::model()->findByPk(554);
//
//        $tma = new TimeManagementAnalyzer($simulation);
//        $tma->calculateAndSaveAssessments();
//
//        $assessments = TimeManagementAggregated::model()->findAllByAttributes([
//            'sim_id' => $simulation->id
//        ]);
//
//        //var_dump($tma->durationsGrouped);
//
//        $values = [];
//        foreach ($assessments as $assessment) {
//            $values[$assessment->slug] = $assessment->value;
//        }
//
//        var_dump($values);
//    }
}