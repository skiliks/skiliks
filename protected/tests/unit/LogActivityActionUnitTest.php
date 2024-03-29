<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Тугай
 * Date: 11.02.13
 * Time: 11:59
 * To change this template use File | Settings | File Templates.
 */

class LogActivityActionUnitTest extends CDbTestCase
{
    use UnitLoggingTrait;
    use UnitTestBaseTrait;

    public function testActivityActionDetail()
    {
        $this->initTestUserAsd();

        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $this->user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $json = EventsManager::getState($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = EventsManager::getState($simulation, [
            [1, 1, 'deactivated', 39735, 'window_uid' => 1],
            [20, 24, 'activated', 39735, 'window_uid' => 2, ['dialogId' => $simulation->game_type->getReplica(['excel_id' => 1])->getPrimaryKey(), 'lastDialogId' => 1]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = EventsManager::getState($simulation, [
            [20, 24, 'deactivated', 40493, 'window_uid' => 2, ['dialogId' => $simulation->game_type->getReplica(['excel_id' => 1])->getPrimaryKey(), 'lastDialogId' => 2]],
            [1, 1, 'activated', 40493, 'window_uid' => 3],
            [1, 1, 'deactivated', 40493, 'window_uid' => 3],
            [20, 23, 'activated', 40493, 'window_uid' => 4, ['dialogId' => $simulation->game_type->getReplica(['excel_id' => 4])->getPrimaryKey(), 'lastDialogId' => 4]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = EventsManager::getState($simulation, [
            [20, 23, 'deactivated', 43992, 'window_uid' => 4, ['dialogId' => $simulation->game_type->getReplica(['excel_id' => 4])->getPrimaryKey(), 'lastDialogId' => 824]],
            [1, 1, 'activated', 43992, 'window_uid' => 5],
            [1, 1, 'deactivated', 44002, 'window_uid' => 5]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $st = array(
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'Window',
                'leg_action' => 'main screen',
                'window' => 1,
                'start_time' => '09:00:00',
                'end_time' => '11:02:15',
                'activity_id' => 'A_wait',
                'window_uid' => 1
            ),
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'ET1.1',
                'window' => 24,
                'start_time' => '11:02:15',
                'end_time' => '11:14:53',
                'activity_id' => 'AE1',
                'window_uid' => 2
            ),
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'E1',
                'window' => 23,
                'start_time' => '11:14:53',
                'end_time' => '12:13:12',
                'activity_id' => 'AE1',
                'window_uid' => 4
            ),
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'Window',
                'leg_action' => 'main screen',
                'window' => 1,
                'start_time' => '12:13:12',
                'end_time' => '12:13:22',
                'activity_id' => 'A_wait',
                'window_uid' => 5

            )
        );

        SimulationService::simulationStop($simulation);
        $activityActions = $simulation->log_activity_actions;
        $data = [];
        foreach ($activityActions as $activityAction) {
            $data[] = [
                'sim_id'      => $activityAction->sim_id,
                'leg_type'    => $activityAction->activityAction->leg_type,
                'leg_action'  => $activityAction->activityAction->getAction()->getCode(),
                'window'      => $activityAction->window,
                'start_time'  => $activityAction->start_time,
                'end_time'    => $activityAction->end_time,
                'activity_id' => $activityAction->activityAction->activity->code,
                'window_uid'  => $activityAction->window_uid
            ];
        }
        $this->assertEquals($st, $data);
    }

    /**
     * Проверка странного бага, в котором звонок Трутневу при открытом сводном бюджете не логируется
     */
    public function testTrutnevDelegation()
    {
        $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $this->user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        $logs = [];
        $template = $simulation->game_type->getDocumentTemplate(['code' => 'D1']);
        $document = MyDocument::model()->findByAttributes(['sim_id' => $simulation->getPrimaryKey(), 'template_id' => $template->getPrimaryKey()]);
        $this->appendWindow($logs, 1);
        $this->appendWindow($logs, 41);
        $this->appendDocument($logs, $document, 60, 122);
        $this->appendPhoneCall($logs, 'RST1', 607);
        $this->appendDocument($logs, $document, 0, 122);
        $this->appendPhoneTalk($logs, 'RS1', 621);
        $this->appendDocument($logs, $document, 0, 122);
        $this->appendPhoneTalk($logs, 'RS1.1', 631);
        $this->appendDocument($logs, $document, 60, 122);
        $events = new EventsManager();
        $events->processLogs($simulation, $logs);
        array_map(function ($action) {$action->dump();}, $simulation->log_activity_actions);
        SimulationService::simulationStop($simulation);

    }

    /*
     * Проверка логов Leg_action detail для E2
     */
    public function testActivityAction2()
    {
        $profile = YumProfile::model()->findByAttributes(['email' => 'asd@skiliks.com']);
        $user = $profile->user;
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $json = EventsManager::getState($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1]
        ]);
        $this->assertEquals(0, $json['result']);
        $call = $simulation->game_type->getReplica(['excel_id' => 124]);
        $callReply = $simulation->game_type->getReplica(['excel_id' => 125]);
        $E2StartReplica = $simulation->game_type->getReplica(['excel_id' => 135]);
        $E2EndReplica = $simulation->game_type->getReplica(['excel_id' => 142]);
        $E24StartReplica = $simulation->game_type->getReplica(['excel_id' => 157]);
        $E24EndReplica = $simulation->game_type->getReplica(['excel_id' => 162]);
        unset($json);
        $json = EventsManager::getState($simulation, [
            [1, 1, 'deactivated', 43339, 'window_uid' => 1],
            [20, 24, 'activated', 43339, 'window_uid' => 2, ['dialogId' => $call->primaryKey, 'lastDialogId' => $call->primaryKey]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = EventsManager::getState($simulation, [
            [20, 24, 'deactivated', 43370, 'window_uid' => 2, ['dialogId' => $call->primaryKey, 'lastDialogId' => $callReply->primaryKey]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);

        $json = EventsManager::getState($simulation, [
            [1, 1, 'activated', 43370, 'window_uid' => 3],
            [1, 1, 'deactivated', 43370, 'window_uid' => 3],
            [20, 23, 'activated', 43370, 'window_uid' => 4, ['dialogId' => $E2StartReplica->primaryKey, 'lastDialogId' => $E2StartReplica->primaryKey]],
            [20, 23, 'deactivated', 43404, 'window_uid' => 4, ['dialogId' => $E2StartReplica->primaryKey, 'lastDialogId' => $E2StartReplica->primaryKey]],
            [1, 1, 'activated', 43404, 'window_uid' => 5],
            [1, 1, 'deactivated', 43404, 'window_uid' => 5],
            [20, 23, 'activated', 43404, 'window_uid' => 6, ['dialogId' => $E2StartReplica->primaryKey, 'lastDialogId' => $E2StartReplica->primaryKey]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);

        $json = EventsManager::getState($simulation, [
            [20, 23, 'deactivated', 43572, 'window_uid' => 6, ['dialogId' => $E2StartReplica->primaryKey, 'lastDialogId' => $E2EndReplica->primaryKey]],
            [1, 1, 'activated', 43572, 'window_uid' => 7],
            [1, 1, 'deactivated', 43572, 'window_uid' => 7],
            [20, 23, 'activated', 43572, 'window_uid' => 8, ['dialogId' => $E24StartReplica->primaryKey, 'lastDialogId' => $E24StartReplica->primaryKey]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);

        $json = EventsManager::getState($simulation, [
            [20, 23, 'deactivated', 43765, 'window_uid' => 8, ['dialogId' => $E24StartReplica->primaryKey, 'lastDialogId' => $E24EndReplica->primaryKey]],
            [1, 1, 'activated', 43765, 'window_uid' => 9],
            [1, 1, 'deactivated', 44444, 'window_uid' => 9]
        ]);

        $this->assertEquals(0, $json['result']);
        unset($json);

        SimulationService::simulationStop($simulation);

        $activityActions = $simulation->log_activity_actions;
        $data = [];
        foreach ($activityActions as $activityAction) {
            $data[] = [
                'sim_id' => $activityAction->sim_id,
                'leg_type' => $activityAction->activityAction->leg_type,
                'leg_action' => $activityAction->activityAction->getAction()->getCode(),
                'start_time' => $activityAction->start_time,
                'end_time' => $activityAction->end_time,
                'activity_id' => $activityAction->activityAction->activity->code,
            ];
        }

        $tmp = array(
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'Window',
                'leg_action' => 'main screen',
                'start_time' => '09:00:00',
                'end_time' => '12:02:19',
                'activity_id' => 'A_wait',
            ),
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'ET2.1',
                'start_time' => '12:02:19',
                'end_time' => '12:02:50',
                'activity_id' => 'AE2a',
            ),
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'E2',
                'start_time' => '12:02:50',
                'end_time' => '12:03:24',
                'activity_id' => 'AE2a',
            ),
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'E2',
                'start_time' => '12:03:24',
                'end_time' => '12:06:12',
                'activity_id' => 'AE2a',
            ),
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'E2.4',
                'start_time' => '12:06:12',
                'end_time' => '12:09:25',
                'activity_id' => 'AE2b',
            ),
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'Window',
                'leg_action' => 'main screen',
                'start_time' => '12:09:25',
                'end_time' => '12:20:44',
                'activity_id' => 'A_wait',
            ),
        );
        $this->assertEquals($tmp, $data);

    }

    /**
       Leg_actions (both Detail & Aggregate) - учитывает,
       куда входят plan, принадлежащие нескольким Activity
       SK-1225,1342
     */
    public function testActivityPriority()
    {

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $this->initTestUserAsd();
            $invite = new Invite();
            $invite->scenario = new Scenario();
            $invite->receiverUser = $this->user;
            $invite->scenario->slug = Scenario::TYPE_FULL;
            $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

            // Create fake Activity {
            $activity = new Activity();
            $activity->code = "WINPA";
            $activity->parent = 'WIN';
            $activity->grandparent = "WINALL";
            $activity->name = "Name1";
            $activity->category_id = "0";
            $activity->numeric_id = 10000;
            $activity->import_id = '1234';
            $activity->type = "Activity";
            $activity->scenario_id = $simulation->game_type->getPrimaryKey();
            $activity->save();

            $activityAction = new ActivityAction();
            $activityAction->activity_id = $activity->getPrimaryKey();
            $activityAction->window_id = 3;
            $activityAction->leg_type = "Window";
            $activityAction->import_id = '1234';
            $activityAction->scenario_id = $simulation->game_type->getPrimaryKey();
            $activityAction->save();
            // Create fake Activity }

            $db = Activity::model()->findByAttributes(['code' => 'WINPA']);
            $this->assertNotNull($db);

            $db2 = ActivityAction::model()->findByAttributes(['activity_id' => $activity->getPrimaryKey(), 'scenario_id' => $simulation->game_type->getPrimaryKey()]);
            $this->assertNotNull($db2);

            $logs = [
                [3, 3, 'activated', 37526, 'window_uid' => 130],
                [3, 3, 'deactivated', 37548, 'window_uid' => 130]
            ];

            EventsManager::processLogs($simulation, $logs);

            // Генерация activityAction {
            LogHelper::updateUniversalLog($simulation);
            $analyzer = new ActivityActionAnalyzer($simulation);
            $analyzer->run();
            // Генерация activityAction }

            $logAction = LogActivityAction::model()->findByAttributes(['sim_id' => $simulation->id, 'window' => 3, 'window_uid' => 130]);
            $this->assertEquals($activityAction->id, $logAction->activity_action_id);

            $resActivity = ActivityAction::model()->findByAttributes(['id' => $logAction->activity_action_id]);
            $this->assertEquals('WINPA', $resActivity->activity->code);

            SimulationService::simulationStop($simulation);

            $transaction->rollback();
        } catch (CException $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    /**
     * Следит за тем, что после того, как завершится Activity, бралась следующая по приоритету
     * на примере MS55
     * SK-1224
     */
    public function testActivityCompletion()
    {
        $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $this->user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        // activate mainScreen.MainScreen
        EventsManager::processLogs($simulation, [[1, 1, 'activated', 32400, 'window_uid' => 1]]);

        $message1 = LibSendMs::sendMsByCode($simulation, 'MS55');
        $message2 = LibSendMs::sendMsByCode($simulation, 'MS55');
        $message3 = LibSendMs::sendMsByCode($simulation, 'MS55');

        $message1->refresh();
        $message2->refresh();
        $message3->refresh();

        $firstDialog = Replica::model()->findByAttributes([
            'excel_id' => 516,
            'scenario_id' => $simulation->scenario_id,
        ]);

        $lastDialog = Replica::model()->findByAttributes([
            'excel_id' => 523,
            'scenario_id' => $simulation->scenario_id,
        ]);

        $logs = [
            [1, 1, 'deactivated', 32500, 'window_uid' => 1],
            [10, 11, 'activated', 32500, 'window_uid' => 2],
            [10, 11, 'deactivated', 32520, 'window_uid' => 2],
            [10, 13, 'activated', 32520, 'window_uid' => 3], # Send mail
            [10, 13, 'deactivated', 32580, 'window_uid' => 3, ['mailId' => $message1->primaryKey]],
            [10, 11, 'activated', 32580, 'window_uid' => 4],
            [10, 11, 'deactivated', 32640, 'window_uid' => 4],
            [10, 13, 'activated', 32640, 'window_uid' => 5], # Send mail
            [10, 13, 'deactivated', 32700, 'window_uid' => 5, ['mailId' => $message2->primaryKey]],
            [10, 11, 'activated', 32700, 'window_uid' => 6],
            [10, 11, 'deactivated', 32760, 'window_uid' => 6],
            [10, 13, 'activated', 32760, 'window_uid' => 7], # Send mail
            [10, 13, 'deactivated', 32820, 'window_uid' => 7, ['mailId' => $message3->primaryKey]],
            [20, 23, 'activated', 32820, ['dialogId' => $firstDialog->primaryKey], 'window_uid' => 8],
            [20, 23, 'deactivated', 32880, ['dialogId' => $firstDialog->primaryKey, 'lastDialogId' => $lastDialog->primaryKey], 'window_uid' => 8],

        ];

        EventsManager::processLogs($simulation, $logs);

        // Генерация activityAction {
        LogHelper::updateUniversalLog($simulation);
        $analyzer = new ActivityActionAnalyzer($simulation);
        $analyzer->run();
        // Генерация activityAction }

        LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);

        /** @var $activity_actions LogActivityAction[] */
        $activity_actions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        array_map(function ($i) {$i->dump();}, $activity_actions);

        foreach ($activity_actions as $actions) {
            echo $activity_actions[2]->activityAction->activity->code . "\n";
        }

        // Test for insert
        $this->assertCount(2, $simulation->completed_parent_activities);
        $this->assertEquals($activity_actions[2]->activityAction->activity->code, 'TMY3');
        $this->assertEquals($activity_actions[4]->activityAction->activity->code, 'A_already_used');
        $this->assertEquals('T2', $activity_actions[7]->activityAction->activity->code);
    }
}
