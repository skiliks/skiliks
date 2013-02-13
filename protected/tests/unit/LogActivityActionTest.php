<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Тугай
 * Date: 11.02.13
 * Time: 11:59
 * To change this template use File | Settings | File Templates.
 */

class LogActivityActionTest extends CDbTestCase {

    public function testActivityActionDetail() {

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        $event = new EventsManager();
        $json = $event->getState($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = $event->getState($simulation, [
            [1, 1, 'deactivated', 39735, 'window_uid' => 1],
            [20, 24, 'activated', 39735, 'window_uid' => 2, ['dialogId' => 1, 'lastDialogId'=>1]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = $event->getState($simulation, [
            [20, 24, 'deactivated', 40493, 'window_uid' => 2, ['dialogId' => 1, 'lastDialogId'=>2]],
            [1, 1, 'activated', 40493, 'window_uid' => 3],
            [1, 1, 'deactivated', 40493, 'window_uid' => 3],
            [20, 23, 'activated', 40493, 'window_uid' => 4, ['dialogId' => 4, 'lastDialogId'=>4]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = $event->getState($simulation, [
            [20, 23, 'deactivated', 43992, 'window_uid' => 4, ['dialogId' => 4, 'lastDialogId'=>824]],
            [1, 1, 'activated', 43992, 'window_uid' => 5],
            [1, 1, 'deactivated', 44002, 'window_uid' => 5]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $st = array (
            0 =>
            array (
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
            array (
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
            array (
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
            array (
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
        $simulation_service->simulationStop($simulation->id);
        $res = LogHelper::getLegActionsDetail(LogHelper::RETURN_DATA, $simulation);
        $this->assertEquals($st, $res['data']);

        //Logger::write(var_export($res, true));


        //Logger::write(var_export($res, true));
    }

    public function testActivityAction2() {

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        $event = new EventsManager();
        $json = $event->getState($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = $event->getState($simulation, [
            [1, 1, 'deactivated', 43339, 'window_uid' => 1],
            [20, 24, 'activated', 43339, 'window_uid' => 2, ['dialogId' => 128, 'lastDialogId'=>128]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);
        $json = $event->getState($simulation, [
            [20, 24, 'deactivated', 43370, 'window_uid' => 2, ['dialogId' => 128, 'lastDialogId'=>129]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);

        $json = $event->getState($simulation, [
            [1, 1, 'activated', 43370, 'window_uid' => 3],
            [1, 1, 'deactivated', 43370, 'window_uid' => 3],
            [20, 23, 'activated', 43370, 'window_uid' => 4, ['dialogId' => 141, 'lastDialogId'=>141]],
            [20, 23, 'deactivated', 43404, 'window_uid' => 4, ['dialogId' => 141, 'lastDialogId'=>141]],
            [1, 1, 'activated', 43404, 'window_uid' => 5],
            [1, 1, 'deactivated', 43404, 'window_uid' => 5],
            [20, 23, 'activated', 43404, 'window_uid' => 6, ['dialogId' => 142, 'lastDialogId'=>142]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);

        $json = $event->getState($simulation, [
            [20, 23, 'deactivated', 43572, 'window_uid' => 6, ['dialogId' => 142, 'lastDialogId'=>147]],
            [1, 1, 'activated', 43572, 'window_uid' => 7],
            [1, 1, 'deactivated', 43572, 'window_uid' => 7],
            [20, 23, 'activated', 43572, 'window_uid' => 8, ['dialogId' => 163, 'lastDialogId'=>163]]
        ]);
        $this->assertEquals(0, $json['result']);
        unset($json);

        $json = $event->getState($simulation, [
            [20, 23, 'deactivated', 43765, 'window_uid' => 8, ['dialogId' => 163, 'lastDialogId'=>168]],
            [1, 1, 'activated', 43765, 'window_uid' => 9],
            [1, 1, 'deactivated', 44444, 'window_uid' => 9]
        ]);

        $this->assertEquals(0, $json['result']);
        unset($json);
        $simulation_service->simulationStop($simulation->id);
        $res2 = LogHelper::getLegActionsDetail(LogHelper::RETURN_DATA, $simulation);
        $tmp = array (
            0 =>
            array (
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
            array (
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
            array (
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
            array (
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
            array (
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
            array (
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

}
