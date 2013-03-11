<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Тугай
 * Date: 11.02.13
 * Time: 11:59
 * To change this template use File | Settings | File Templates.
 */

class LogActivityActionTest extends CDbTestCase
{
    use UnitLoggingTrait;

    public function testActivityActionDetail()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $json = EventsManager::getState($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = EventsManager::getState($simulation, [
            [1, 1, 'deactivated', 39735, 'window_uid' => 1],
            [20, 24, 'activated', 39735, 'window_uid' => 2, ['dialogId' => 1, 'lastDialogId' => 1]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = EventsManager::getState($simulation, [
            [20, 24, 'deactivated', 40493, 'window_uid' => 2, ['dialogId' => 1, 'lastDialogId' => 2]],
            [1, 1, 'activated', 40493, 'window_uid' => 3],
            [1, 1, 'deactivated', 40493, 'window_uid' => 3],
            [20, 23, 'activated', 40493, 'window_uid' => 4, ['dialogId' => 4, 'lastDialogId' => 4]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = EventsManager::getState($simulation, [
            [20, 23, 'deactivated', 43992, 'window_uid' => 4, ['dialogId' => 4, 'lastDialogId' => 824]],
            [1, 1, 'activated', 43992, 'window_uid' => 5],
            [1, 1, 'deactivated', 44002, 'window_uid' => 5]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $st = array(
            0 =>
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'Window',
                'leg_action' => 'main screen',
                'mail_id' => '',
                'dialog_code' => NULL,
                'mail_code' => NULL,
                'doc_code' => NULL,
                'subtype' => 'main screen',
                'category' => '5',
                'is_keep_last_category' => '0',
                'start_time' => '09:00:00',
                'end_time' => '11:02:15',
                'diff_time' => '02:02:15',
                'activity_id' => 'A_wait',
            ),
            1 =>
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'ET1.1',
                'mail_id' => '',
                'dialog_code' => 'ET1.1',
                'mail_code' => NULL,
                'doc_code' => NULL,
                'subtype' => NULL,
                'category' => '1',
                'is_keep_last_category' => '0',
                'start_time' => '11:02:15',
                'end_time' => '11:14:53',
                'diff_time' => '00:12:38',
                'activity_id' => 'AE1',
            ),
            2 =>
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'E1',
                'mail_id' => '',
                'dialog_code' => 'E1',
                'mail_code' => NULL,
                'doc_code' => NULL,
                'subtype' => NULL,
                'category' => '1',
                'is_keep_last_category' => '0',
                'start_time' => '11:14:53',
                'end_time' => '12:13:12',
                'diff_time' => '00:58:19',
                'activity_id' => 'AE1',
            ),
            3 =>
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'Window',
                'leg_action' => 'main screen',
                'mail_id' => '',
                'dialog_code' => NULL,
                'mail_code' => NULL,
                'doc_code' => NULL,
                'subtype' => 'main screen',
                'category' => '5',
                'is_keep_last_category' => '0',
                'start_time' => '12:13:12',
                'end_time' => '12:13:22',
                'diff_time' => '00:00:10',
                'activity_id' => 'A_wait',
            ));

        SimulationService::simulationStop($simulation);
        $res = LogHelper::getLegActionsDetail(LogHelper::RETURN_DATA, $simulation);
        $this->assertEquals($st, $res['data']);
    }

    /*
     * Проверка логов Leg_action detail для E2
     */
    public function testActivityAction2()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        $json = EventsManager::getState($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1]
        ]);
        $this->assertEquals(0, $json['result']);
        $call = Replica::model()->findByAttributes(['excel_id' => 124]);
        $callReply = Replica::model()->findByAttributes(['excel_id' => 125]);
        $E2StartReplica = Replica::model()->findByAttributes(['excel_id' => 135]);
        $E2EndReplica = Replica::model()->findByAttributes(['excel_id' => 142]);
        $E24StartReplica = Replica::model()->findByAttributes(['excel_id' => 157]);
        $E24EndReplica = Replica::model()->findByAttributes(['excel_id' => 162]);
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

        $res2 = LogHelper::getLegActionsDetail(LogHelper::RETURN_DATA, $simulation);
        $tmp = array(
            0 =>
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'Window',
                'leg_action' => 'main screen',
                'mail_id' => '',
                'dialog_code' => NULL,
                'mail_code' => NULL,
                'doc_code' => NULL,
                'subtype' => 'main screen',
                'category' => '5',
                'is_keep_last_category' => '0',
                'start_time' => '09:00:00',
                'end_time' => '12:02:19',
                'diff_time' => '03:02:19',
                'activity_id' => 'A_wait',
            ),
            1 =>
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'ET2.1',
                'mail_id' => '',
                'dialog_code' => 'ET2.1',
                'mail_code' => NULL,
                'doc_code' => NULL,
                'subtype' => NULL,
                'category' => '1',
                'is_keep_last_category' => '0',
                'start_time' => '12:02:19',
                'end_time' => '12:02:50',
                'diff_time' => '00:00:31',
                'activity_id' => 'AE2a',
            ),
            2 =>
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'E2',
                'mail_id' => '',
                'dialog_code' => 'E2',
                'mail_code' => NULL,
                'doc_code' => NULL,
                'subtype' => NULL,
                'category' => '1',
                'is_keep_last_category' => '0',
                'start_time' => '12:02:50',
                'end_time' => '12:03:24',
                'diff_time' => '00:00:34',
                'activity_id' => 'AE2a',
            ),
            3 =>
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'E2',
                'mail_id' => '',
                'dialog_code' => 'E2',
                'mail_code' => NULL,
                'doc_code' => NULL,
                'subtype' => NULL,
                'category' => '1',
                'is_keep_last_category' => '0',
                'start_time' => '12:03:24',
                'end_time' => '12:06:12',
                'diff_time' => '00:02:48',
                'activity_id' => 'AE2a',
            ),
            4 =>
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'System_dial_leg',
                'leg_action' => 'E2.4',
                'mail_id' => '',
                'dialog_code' => 'E2.4',
                'mail_code' => NULL,
                'doc_code' => NULL,
                'subtype' => NULL,
                'category' => '2',
                'is_keep_last_category' => NULL,
                'start_time' => '12:06:12',
                'end_time' => '12:09:25',
                'diff_time' => '00:03:13',
                'activity_id' => 'AE2b',
            ),
            5 =>
            array(
                'sim_id' => $simulation->id,
                'leg_type' => 'Window',
                'leg_action' => 'main screen',
                'mail_id' => '',
                'dialog_code' => NULL,
                'mail_code' => NULL,
                'doc_code' => NULL,
                'subtype' => 'main screen',
                'category' => '5',
                'is_keep_last_category' => '0',
                'start_time' => '12:09:25',
                'end_time' => '12:20:44',
                'diff_time' => '00:11:19',
                'activity_id' => 'A_wait',
            ),
        );
        $this->assertEquals($tmp, $res2['data']);
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
            $activity = new Activity();
            $activity->id = "WINPA";
            $activity->parent = 'WIN';
            $activity->grandparent = "WINALL";
            $activity->name = "Name1";
            $activity->category_id = "2_min";
            $activity->numeric_id = 10000;
            $activity->type = "Activity";
            $activity->save();
            $activityAction = new ActivityAction();
            $activityAction->activity_id = "WINPA";
            $activityAction->window_id = 3;
            $activityAction->leg_type = "Window";
            $activityAction->save();
            $db = Activity::model()->findByAttributes(['id' => 'WINPA']);
            $this->assertNotNull($db);
            $db2 = ActivityAction::model()->findByAttributes(['activity_id' => 'WINPA']);
            $this->assertNotNull($db2);

            $user = YumUser::model()->findByAttributes(['username' => 'asd']);
            $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
            $logs = [
                [3, 3, 'activated', 37526, 'window_uid' => 130],
                [3, 3, 'deactivated', 37548, 'window_uid' => 130]
            ];

            EventsManager::processLogs($simulation, $logs);
            $logAction = LogActivityAction::model()->findByAttributes(['sim_id' => $simulation->id, 'window' => 3, 'window_uid' => 130]);
            $this->assertEquals($activityAction->id, $logAction->activity_action_id);
            $resActivity = ActivityAction::model()->findByAttributes(['id' => $logAction->activity_action_id]);
            $this->assertEquals('WINPA', $resActivity->activity_id);

            SimulationService::simulationStop($simulation);

            $transaction->rollback();
        } catch (CException $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    /**
     * Следит за тем, что после того, как завершится Activity, бралась следующая по приоритету
     * SK-1224
     */
    public function testActivityCompletion()
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $user = YumUser::model()->findByAttributes(['username' => 'asd']);
            $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

            $options = new SendMailOptions();
            $options->phrases = '';
            $options->copies = '';
            $options->messageId = MailTemplate::model()->findByAttributes(['code' => 'MS55'])->primaryKey;
            $options->subject_id = CommunicationTheme::model()->findByAttributes(['code' => 71])->primaryKey;
            $options->setRecipientsArray(Character::model()->findByAttributes(['code' => 39])->primaryKey);
            $options->senderId = Character::HERO_ID;
            $options->time = '11:00:00';
            $options->setLetterType('new');
            $options->groupId = MailBox::FOLDER_OUTBOX_ID;
            $options->simulation = $simulation;

            $message1 = MailBoxService::sendMessagePro($options);
            $message2 = MailBoxService::sendMessagePro($options);
            $message3 = MailBoxService::sendMessagePro($options);

            $first_dialog = Replica::model()->findByAttributes(['excel_id' => 516]);
            $last_dialog = Replica::model()->findByAttributes(['excel_id' => 523]);

            $logs = [
                [1, 1, 'activated', 32400, 'window_uid' => 1],
                [1, 1, 'deactivated', 32460, 'window_uid' => 1],
                [10, 11, 'activated', 32460, 'window_uid' => 2],
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
                [20, 23, 'activated', 32820, ['dialogId' => $first_dialog->primaryKey], 'window_uid' => 1], # Send mail
                [20, 23, 'deactivated', 32880, ['dialogId' => $first_dialog->primaryKey, 'lastDialogId' => $last_dialog->primaryKey], 'window_uid' => 8], # Send mail

            ];

            EventsManager::processLogs($simulation, $logs);

            LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);

            /** @var $activity_actions LogActivityAction[] */
            $activity_actions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->id]);
            array_map(function ($i) {$i->dump();}, $activity_actions);

            // Test for insert
            $this->assertCount(2, $simulation->completed_parent_activities);
            $this->assertEquals($activity_actions[2]->activityAction->activity_id, 'TMY3');
            $this->assertEquals($activity_actions[4]->activityAction->activity_id, 'A_already_used');
            $this->assertEquals('T2', $activity_actions[7]->activityAction->activity_id);
            $transaction->rollback();
        } catch (CException $e) {
            $transaction->rollback();
            throw $e;
        }
    }

}
