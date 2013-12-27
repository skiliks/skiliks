<?php

class FlagServiceUnitTest extends CDbTestCase
{
    /**
     * Service method
     *
     * @param $simulation
     * @param $newHours
     * @param $newMinutes
     * @param bool $s
     */
    public function setTime($simulation, $newHours, $newMinutes, $s = true)
    {
        SimulationService::setSimulationClockTime(
            $simulation,
            $newHours,
            $newMinutes
        );
        if ($s == true) {
            $simulation->deleteOldTriggers($newHours, $newMinutes);
        }

    }
    /**
     * Проверяет, устанавливаются ли флаги, при выборе определенной реплики
     */
    public function testDialogFlagSet()
    {
        //$this->markTestSkipped();

        /** @var $user Users */
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $dialogService = new DialogService();

        $dialogService->getDialog(
            $simulation->id,
            $simulation->game_type->getReplica(['excel_id' => 35])->id,
            '11:00'
        );
        $dialogService->getDialog(
            $simulation->id,
            $simulation->game_type->getReplica(['excel_id' => 50])->id,
            '11:00'
        );
        $dialogService->getDialog(
            $simulation->id,
            $simulation->game_type->getReplica(['excel_id' => 70])->id,
            '11:00'
        );

        $flags = FlagsService::getFlagsState($simulation);

        $this->assertEquals($flags['F3'], '1');
        $this->assertEquals($flags['F4'], '1');
        $this->assertEquals($flags['F13'], '1');
    }

    /**
     * Проверяет то, что письмо после флага приходит с правильным временем
     */
    public function testFlagMailTimeSet()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        LibSendMs::sendMs($simulation, 'MS30', false, '', '10:00');
        Yii::app()->session['gameTime'] = '10:00'; // вместо передачи времени в POST-запросе

        $eventManager = new EventsManager();
        $result = $eventManager->getState($simulation, []);

        $this->assertEquals($result['events'][0]['eventType'],'M');

        $mailManager = new MailBoxService();
        $inboxMailList = $mailManager->getMessages([
            'folderId' =>1,
            'simId'    => $simulation->getPrimaryKey()
        ]);
        $outboxMailList = $mailManager->getMessages([
            'folderId' =>3,
            'simId'    => $simulation->getPrimaryKey()
        ]);

        // get from array email 'MS30'
        // Федоров А.В. -> Трудякин Е., 'Срочно жду бюджет логистики'
        $MS30 = array_values(array_filter($outboxMailList, function ($mailItem) {
            return $mailItem['template'] == 'MS30';
        }))[0];

        // get from array email 'M31'
        // Трудякин Е. -> Федоров А.В., 'Re: Срочно жду бюджет логистики'
        $M31 = array_values(array_filter($inboxMailList, function ($mailItem) {
            return $mailItem['template'] == 'M31';
        }))[0];

        // '04.10.2013 09:45' or // '04.10.2013 09:46'
        // $this->assertEquals($MS30['sentAt'], $M31['sentAt']);
    }

    /**
     * Тест на установку флага, при отправке правильного письма
     */
    public function testSentMailFlagSet()
    {
        //$this->markTestSkipped();

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $mail = LibSendMs::sendMs($simulation, 'MS30');
        MailBoxService::updateMsCoincidence($mail->id, $simulation->id);

        $mail = LibSendMs::sendMs($simulation, 'MS30');
        MailBoxService::updateMsCoincidence($mail->id, $simulation->id);

        $flags = FlagsService::getFlagsState($simulation);

        $this->assertEquals($flags['F30'], '1');
        $this->assertEquals($flags['F31'], '1');
    }

    /**
     * Проверяет что на фронтенд попадают только правильные реплики по диалогу S2
     *
     */
    public function testBlockReplica()
    {
        //$this->markTestSkipped();

        /** @var $user Users */
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $FullScenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $dialog = new DialogService();
        $replica_599 = Replica::model()->findByAttributes(
            [
                'excel_id'    => 599, // dialog T7.3
                'scenario_id' => $FullScenario->id
            ]
        );

        FlagsService::setFlag($simulation, 'F38_3', 1); // нужен для старта события 'T7.4'

        // case 1, флаг F22 выключен {
        $result = $dialog->getDialog($simulation->id, $replica_599->id, 13000);

        foreach ($result['events'][0]['data'] as $replica) {
            if($replica['ch_from'] == 1) {
                $this->assertEquals($replica['excel_id'], 600); // 600 это excel_id правильной риплаки
                $data[] = $replica['excel_id'];
            }
        }
        unset($replica, $result);
        // case 1, флаг F22 выключен }

        // case 2, флаг F22 включен {
        FlagsService::setFlag($simulation, 'F22', 1); // нужен для старта события 'T7.4'
        $result = $dialog->getDialog($simulation->id, $replica_599->id, 13000);

        foreach ($result['events'][0]['data'] as $replica) {
            if($replica['ch_from'] == 1) {
                $this->assertEquals($replica['excel_id'], 601); // 600 это excel_id правильной риплаки
                $data[] = $replica['excel_id'];
            }
        }
        // case 2, флаг F22 включен }
    }

    /**
     * Проверяет что диалог прокируется если не выставлен флаг
     */
    public function testBlockDialog()
    {
        //$this->markTestSkipped(); // S

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        FlagsService::setFlag($simulation, 'F4', 0);

        // Case 1: block event
        EventsManager::startEvent($simulation, 'ET1.3.1');

        $result = EventsManager::getState($simulation, []);

        $this->assertFalse(isset($result['events']));

        // Case 2: run event
        FlagsService::setFlag($simulation, 'F4', 1);

        EventsManager::startEvent($simulation, 'ET1.3.1');

        $result2 = EventsManager::getState($simulation, []);

        $this->assertTrue(isset($result2['events']));
    }

    /**
     * Проверяет блокировку по флагу F14 собитыя E12
     * Тест эмулирует не просто запуск E12, а ответ на реплику в ЕТ12.3 которая приводит к Е12
     */
    public function testBlockDialogByGetDialog()
    {
        //$this->markTestSkipped();

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        FlagsService::setFlag($simulation, 'F14', 0);

        $e = new EventsManager();
        EventsManager::startEvent($simulation, 'ET12.3');

        EventsManager::getState($simulation, []);

        $dialog = new DialogService();
        $json = $dialog->getDialog($simulation->id, Replica::model()->findByAttributes(['excel_id' => 419])->primaryKey, '09:10:00');

        $this->assertEquals(0, count($json['events']));
    }

    /**
     * Проверяет блокировку по флагу F3 собитыя E1.2.1
     */
    public function testBlockDialogByPhone()
    {
        ////$this->markTestSkipped();

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        // Case 1: block event
        $e = new EventsManager();
        EventsManager::startEvent($simulation, 'E1.2.1');

        $result = EventsManager::getState($simulation, []);

        $this->assertFalse(isset($result['events']));

        // Case 2: run event
        FlagsService::setFlag($simulation, 'F3', 1);

        $e = new EventsManager();
        EventsManager::startEvent($simulation, 'E1.2.1');

        $result2 = EventsManager::getState($simulation, []);

        $this->assertTrue(isset($result2['events']));
    }

    /**
     * Проверяет что письмо M31 отправляется по флагу F30, а M9 при флаге F16 — нет
     */
    public function testSendEmailAfterFlagSwitched()
    {
        //$this->markTestSkipped();

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        FlagsService::setFlag($simulation, 'F30', 1);
        FlagsService::setFlag($simulation, 'F16', 1);

        $e = new EventsManager();

        $result = EventsManager::getState($simulation, []);
        $this->assertEquals(1, count($result['events']));

        $result = EventsManager::getState($simulation, []);
        $this->assertEquals(0, $result['result']);

        /** @var $email MailBox */
        $email = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M31'
        ]);
        /** @var $time_email MailBox */
        $time_email = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M9'
        ]);

        $this->assertEquals('inbox', $email->getGroupName());
        $this->assertNull($time_email);

        Yii::app()->session['gameTime'] = '13:30';
        $i = 0;
        while (true) {
            $state = EventsManager::getState($simulation, []);
            $i++;
            if ($state['result'] == 0) {
                break;
            }
        };
        /** @var $timed_good_email MailBox */
        $timed_good_email = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M31',
        ]);
        /** @var $timed_bad_email MailBox */
        $timed_bad_email = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M9',
        ]);

        $this->assertEquals('inbox', $timed_good_email->getGroupName());
        $this->assertNotNull($timed_bad_email);
        $this->assertEquals('inbox', $timed_bad_email->getGroupName());
    }

    /*
     * Проверка ET12.1 для флага F14 чтоб была кнопка "Ответить"
     */

    public function testNewFlagsRules() {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        FlagsService::setFlag($simulation, 'F14', 1);

        EventsManager::startEvent($simulation, 'ET12.1');

        $result = EventsManager::getState($simulation, []);

        $this->assertEquals(3, count($result['events'][0]['data']));
    }

    /*
     * Проверяет ET12.2 для F14 что есть нужная реплика excel_id = 408
     */

    public function testNewFlagsRulesByDialogGet() {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $dialog = new DialogService();
        FlagsService::setFlag($simulation, 'F14', 1);
        $id = Replica::model()->findByAttributes(['code'=>'ET12.1', 'replica_number'=> 1, 'step_number'=> 1])->id;

        $result = $dialog->getDialog($simulation->id, $id, '9:00');

        $this->assertEquals('408', $result['events'][0]['data'][1]['excel_id']);
    }

    /*
     * Проверяет что оба папаметра у реплики (flag_to_switch и flag_to_switch_2) переключают флаги
     * и делаю это вовремя
     */
    public function testFlagToSwitch2() {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $dialog = new DialogService();
        //FlagsService::setFlag($simulation, 'F14', 1);
        $flag = FlagsService::getFlag($simulation, "F22");
        $this->assertEquals('0', $flag->value);
        $flag = FlagsService::getFlag($simulation, "F38_3");
        $this->assertEquals('0', $flag->value);
        $id = Replica::model()->findByAttributes(['code'=>'T7.3', 'flag_to_switch'=> 'F38_3', 'flag_to_switch_2'=> 'F22'])->id;
        $dialog->getDialog($simulation->id, $id, '9:45');
        $flag = FlagsService::getFlag($simulation, "F22");
        $this->assertEquals('1', $flag->value);
        $flag = FlagsService::getFlag($simulation, "F38_3");
        $this->assertEquals('0', $flag->value);
        $flag = SimulationFlagQueue::model()->findByAttributes(['sim_id'=>$simulation->id, 'flag_code'=>'F38_3']);
        $this->assertEquals("11:45", (new DateTime($flag->switch_time))->format("H:i"));
        $this->setTime($simulation, 11, 47);
        FlagsService::checkFlagsDelay($simulation);
        $flag = FlagsService::getFlag($simulation, "F38_3");
        //var_dump($flag->flag);
        $this->assertEquals('1', $flag->value);
    }

    public function testMeetingFlags()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        FlagsService::setFlag($simulation, 'F52', 1);
        FlagsService::setFlag($simulation, 'F51', 1);
        $meetings = MeetingService::getList($simulation);
        $this->assertCount(2, $meetings);

        FlagsService::setFlag($simulation, 'F47', 1);
        $meetings = MeetingService::getList($simulation);
        $this->assertCount(3, $meetings);

        FlagsService::setFlag($simulation, 'F49', 1);
        $meetings = MeetingService::getList($simulation);
        $this->assertCount(4, $meetings);
    }
}
