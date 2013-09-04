<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 4/10/13
 * Time: 8:15 PM
 * To change this template use File | Settings | File Templates.
 */

class TimeManagementAssessmentUnitTest extends CDbTestCase
{
    use UnitLoggingTrait;

    /**
     * Каждого типа лога по 1 штуке
     */
    public function testTimeManagementAssessment_case1()
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

        // log10, 1st priority mail {
        $template_M78 = $simulation->game_type->getMailTemplate(['code' => 'M78']);

        $activity_ARS2 = $simulation->game_type->getActivity(['code' => 'ARS2']);

        $activity_action_M78 = $simulation->game_type->getActivityAction([
            'activity_id' => $activity_ARS2->id,
            'mail_id' => $template_M78->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_MANUAL_DIAL;
        $log->leg_action = 'M78';
        $log->activity_action_id = $activity_action_M78->id;
        $log->activityAction = $activity_action_M78;
        $log->category = $activity_ARS2->category_id;
        $log->keep_last_category_after_60_sec = LogActivityActionAgregated::KEEP_LAST_CATEGORY_YES;
        $log->start_time = '11:30:00';
        $log->end_time = '11:40:00';
        $log->duration = '00:10:00';
        $log->save();
        // log10, 1st priority mail }

        // log11, non priority meeting {
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
        $log->start_time = '11:40:00';
        $log->end_time = '11:45:00';
        $log->duration = '00:10:00';
        $log->save();
        // log11, non priority meeting }

        // log12, A_wait {
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
        $log->start_time = '11:45:00';
        $log->end_time = '11:55:00';
        $log->duration = '00:10:00';
        $log->save();
        // log12, A_wait }

        // log13, A_wait {
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
        $log->start_time = '11:55:00';
        $log->end_time = '12:00:00';
        $log->duration = '00:05:00';
        $log->save();
        // log13, A_wait }

        // log14, A_wait {
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
        // log14, A_wait }

        // log15, A_wait {
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
        // log15, A_wait }

        // log16, Meeting {
        $meeting = $simulation->game_type->getMeeting(['code'=>'MEE1']);

        $activity_action = $simulation->game_type->getActivityAction([
            'meeting_id' => $meeting->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_MEETING;
        $log->leg_action = "MEE1";
        $log->activity_action_id = $activity_action->id;
        $log->activityAction = $activity_action;
        $log->category = $activity_action->activity->category->code;
        $log->start_time = '13:05:00';
        $log->end_time = '13:10:00';
        $log->duration = '00:05:00';
        $log->save();
        // log16, Meeting }

        // log17, Meeting {
        $meeting = $simulation->game_type->getMeeting(['code'=>'MEE2']);

        $activity_action = $simulation->game_type->getActivityAction([
            'meeting_id' => $meeting->id
        ]);
        $log = new LogActivityActionAgregated();
        $log->sim_id = $simulation->id;
        $log->leg_type = ActivityAction::LEG_TYPE_MEETING;
        $log->leg_action = "MEE2";
        $log->activity_action_id = $activity_action->id;
        $log->activityAction = $activity_action;
        $log->category = $activity_action->activity->category->code;
        $log->start_time = '13:10:00';
        $log->end_time = '13:20:00';
        $log->duration = '00:10:00';
        $log->save();
        // log17, Meeting }

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
            54.00, // %
            $values['time_spend_for_1st_priority_activities'],
            'time_spend_for_1st_priority_activities'
        );

        $this->assertEquals(
            27.00, // %
            $values['time_spend_for_non_priority_activities'],
            'time_spend_for_non_priority_activities'
        );

        $this->assertEquals(
            19.00, // %
            $values['time_spend_for_inactivity'],
            'time_spend_for_inactivity'
        );

        $this->assertEquals(
            45, // min
            $values['1st_priority_documents'],
            '1st_priority_documents'
        );

        $this->assertEquals(
            15, // min
            $values['1st_priority_meetings'],
            '1st_priority_meetings'
        );

        $this->assertEquals(
            10, // min
            $values['1st_priority_phone_calls'],
            '1st_priority_phone_calls '
        );

        $this->assertEquals(
            20, // min
            $values['1st_priority_mail'],
            '1st_priority_mail'
        );

        $this->assertEquals(
            10, // min
            $values['1st_priority_planning'],
            '1st_priority_planning'
        );

        $this->assertEquals(
            45, // min
            $values['1st_priority_documents'],
            '1st_priority_documents'
        );


        $this->assertEquals(
            10, // min
            $values['non_priority_documents'],
            'non_priority_documents'
        );

        $this->assertEquals(
            20, // min
            $values['non_priority_meetings'],
            'non_priority_meetings'
        );

        $this->assertEquals(
            10, // min
            $values['non_priority_phone_calls'],
            'non_priority_phone_calls'
        );

        $this->assertEquals(
            10, // min
            $values['non_priority_mail'],
            'non_priority_mail'
        );

        $this->assertEquals(
            0, // min
            $values['non_priority_planning'],
            'non_priority_planning'
        );

        $this->assertEquals(
            40.00, // minutes
            $values['workday_overhead_duration'],
            'workday_overhead_duration'
        );

        $this->assertEquals(
            58.22, // percentage
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
            0, // min
            $values['1st_priority_documents'],
            '1st_priority_documents'
        );

        $this->assertEquals(
            0, // min
            $values['1st_priority_meetings'],
            '1st_priority_meetings'
        );

        $this->assertEquals(
            0, // min
            $values['1st_priority_phone_calls'],
            '1st_priority_phone_calls '
        );

        $this->assertEquals(
            0, // min
            $values['1st_priority_mail'],
            '1st_priority_mail'
        );

        $this->assertEquals(
            0, // min
            $values['1st_priority_planning'],
            '1st_priority_planning'
        );

        $this->assertEquals(
            0, // min
            $values['non_priority_documents'],
            'non_priority_documents'
        );

        $this->assertEquals(
            0, // min
            $values['non_priority_meetings'],
            'non_priority_meetings'
        );

        $this->assertEquals(
            0, // min
            $values['non_priority_phone_calls'],
            'non_priority_phone_calls'
        );

        $this->assertEquals(
            0, // min
            $values['non_priority_mail'],
            'non_priority_mail'
        );

        $this->assertEquals(
            0.00, // min
            $values['non_priority_planning'],
            'non_priority_planning'
        );

        $this->assertEquals(
            0, // minutes
            $values['workday_overhead_duration'],
            'workday_overhead_duration'
        );

        $this->assertEquals(
            33.33, // percentage
            $values['efficiency'],
            'efficiency'
        );
    }

    public function testimeManagementAssessment_case2_for_lite()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_LITE;
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
            $values['time_spend_for_inactivity'],
            'time_spend_for_inactivity'
        );
    }

    public function testEfficiency_workday_ended_1800() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

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
        $log->start_time = '09:45:00';
        $log->end_time = '12:45:00';
        $log->duration = '03:00:00';
        $log->save();
        // log13, A_wait }



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
        $log->start_time = '12:45:00';
        $log->end_time = '18:00:00';
        $log->duration = '05:15:00';
        $log->save();

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
            64.00, // percentage
            $values['time_spend_for_1st_priority_activities'],
            'time_spend_for_1st_priority_activities'
        );

        $this->assertEquals(
            76.00, // 40.00 * 0.6
            $values['efficiency'],
            'efficiency'
        );
    }

    public function testEfficiency_workday_ended_1900() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

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
        $log->start_time = '09:45:00';
        $log->end_time = '13:45:00';
        $log->duration = '04:00:00';
        $log->save();
        // log13, A_wait }



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
        $log->start_time = '13:45:00';
        $log->end_time = '19:00:00';
        $log->duration = '05:15:00';
        $log->save();

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
            57.00, // percentage
            $values['time_spend_for_1st_priority_activities'],
            'time_spend_for_1st_priority_activities'
        );

        $this->assertEquals(
            54.67, // 40.00 * 0.6
            $values['efficiency'],
            'efficiency'
        );
    }

    public function testEfficiency_workday_ended_2000() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

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
        $log->start_time = '09:45:00';
        $log->end_time = '14:45:00';
        $log->duration = '05:00:00';
        $log->save();
        // log13, A_wait }



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
        $log->start_time = '14:45:00';
        $log->end_time = '20:00:00';
        $log->duration = '05:15:00';
        $log->save();

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
            51.00, // percentage
            $values['time_spend_for_1st_priority_activities'],
            'time_spend_for_1st_priority_activities'
        );

        $this->assertEquals(
            34.00, // 40.00 * 0.6
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