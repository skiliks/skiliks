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

    public function testimeManagementAssessment_case1()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        //$simulation = Simulation::model()->findByPk();

        // log1, 1st doc {
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
        // log1, 1st doc }

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

        $tma = new TimeManagementAnalyzer($simulation);
        $tma->prepareDurationsForCalculation();

        var_dump($tma->durationsGrouped);
    }
}