<?php

class FlagServiceTest extends CDbTestCase
{
    /**
     * Проверяет, устанавливаются ли флаги, при выборе определенной реплики
     */
    public function testDialogFlagSet()
    {
        //$this->markTestSkipped();

        /** @var $user Users */
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(2, $user);

        $dialogService = new DialogService();

        $dialogService->getDialog(
            $simulation->id,
            Replica::model()->byExcelId(35)->find()->id,
            '11:00'
        );
        $dialogService->getDialog(
            $simulation->id,
            Replica::model()->byExcelId(50)->find()->id,
            '11:00'
        );
        $dialogService->getDialog(
            $simulation->id,
            Replica::model()->byExcelId(70)->find()->id,
            '11:00'
        );

        $flags = FlagsService::getFlagsState($simulation);

        $this->assertEquals($flags['F3'], '1');
        $this->assertEquals($flags['F4'], '1');
        $this->assertEquals($flags['F13'], '1');
    }

    /**
     * Тест на установку флага, при отправке правильного письма
     */
    public function testSentMailFlagSet()
    {
        //$this->markTestSkipped();

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(2, $user);

        // null prefix
        $receiverId = Character::model()->findByAttributes(['code' => '12'])->primaryKey;

        $msgParams = new SendMailOptions();
        $msgParams->simulation = $simulation;
        $msgParams->subject_id = CommunicationTheme::model()->findByAttributes([
            'code' => 55, 'character_id' => $receiverId, 'mail_prefix' => null])->primaryKey; // 55?
        $msgParams->setRecipientsArray($receiverId);
        $msgParams->groupId = MailBox::FOLDER_OUTBOX_ID;
        $msgParams->time = '11:00';
        $msgParams->messageId = 0;
        $msgParams->copies = '';
        $msgParams->phrases = '';

        $mail = MailBoxService::sendMessagePro($msgParams);
        MailBoxService::updateMsCoincidence($mail->id, $simulation->id);

        // RE: RE:
        $msgParams->subject_id = CommunicationTheme::model()->findByAttributes([
            'code' => 55, 'character_id' => $receiverId,  // 55?
            'mail_prefix' => 'rere', 'theme_usage' => 'mail_outbox'
        ])->primaryKey;

        $mail = MailBoxService::sendMessagePro($msgParams);
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
        $simulation = SimulationService::simulationStart(1, $user);
        // case 1

        EventsManager::startEvent($simulation, 'S2', false, false, 0);

        $data = [];
        //case 1
        $result = EventsManager::getState($simulation, []);
        foreach ($result['events'][0]['data'] as $replica) {
            if($replica['ch_from'] == 1) {
                $this->assertFalse(in_array($replica['excel_id'], $data));
                $data[] = $replica['excel_id'];
            }
        }

        // case 2
        FlagsService::setFlag($simulation, 'F1', 1);

        EventsManager::startEvent($simulation, 'S2', true, true, 0);

       $result = EventsManager::getState($simulation, []);
       foreach ($result['events'][0]['data'] as $replica) {
           if($replica['ch_from'] == 1) {
               $this->assertFalse(in_array($replica['excel_id'], $data));
               $data[] = $replica['excel_id'];
           }
        }

        //case3
        FlagsService::setFlag($simulation, 'F1', 0);
        FlagsService::setFlag($simulation, 'F2', 1);

        EventsManager::startEvent($simulation, 'S2', true, true, 0);

        $result = EventsManager::getState($simulation, []);
        foreach ($result['events'][0]['data'] as $replica) {
            if($replica['ch_from'] == 1) {
                $this->assertFalse(in_array($replica['excel_id'], $data));
                $data[] = $replica['excel_id'];
            }
        }

        //case 4
        FlagsService::setFlag($simulation, 'F2', 0);
        FlagsService::setFlag($simulation, 'F12', 1);

        EventsManager::startEvent($simulation, 'S2', true, true, 0);

        $result = EventsManager::getState($simulation, []);
        foreach ($result['events'][0]['data'] as $replica) {
            if($replica['ch_from'] == 1) {
                $this->assertFalse(in_array($replica['excel_id'], $data));
                $data[] = $replica['excel_id'];
            }
        }
    }

    /**
     * Проверяет что диалог прокируется если не выставлен флаг
     */
    public function testBlockDialog()
    {
        //$this->markTestSkipped(); // S

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(1, $user);

        FlagsService::setFlag($simulation, 'F4', 0);

        // Case 1: block event
        EventsManager::startEvent($simulation, 'ET1.3.1', false, false, 0);

        $result = EventsManager::getState($simulation, []);

        $this->assertFalse(isset($result['events']));

        // Case 2: run event
        FlagsService::setFlag($simulation, 'F4', 1);

        EventsManager::startEvent($simulation, 'ET1.3.1', false, false, 0);

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
        $simulation = SimulationService::simulationStart(1, $user);

        FlagsService::setFlag($simulation, 'F14', 0);

        $e = new EventsManager();
        EventsManager::startEvent($simulation, 'ET12.3', false, false, 0);

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
        $simulation = SimulationService::simulationStart(1, $user);

        // Case 1: block event
        $e = new EventsManager();
        EventsManager::startEvent($simulation, 'E1.2.1', false, false, 0);

        $result = EventsManager::getState($simulation, []);

        $this->assertFalse(isset($result['events']));

        // Case 2: run event
        FlagsService::setFlag($simulation, 'F3', 1);

        $e = new EventsManager();
        EventsManager::startEvent($simulation, 'E1.2.1', false, false, 0);

        $result2 = EventsManager::getState($simulation, []);

        $this->assertTrue(isset($result2['events']));
    }

    /**
     * Проверяет что письмо M31 отправляется по флагу F30, а M9 при флаге M16 — нет
     */
    public function testSendEmailAfterFlagSwitched()
    {
        //$this->markTestSkipped();

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(1, $user);

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

        SimulationService::setSimulationClockTime($simulation, 16, 0);
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
            'code'   => 'M8'
        ]);
        /** @var $timed_bad_email MailBox */
        $timed_bad_email = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M9'
        ]);
        $this->assertEquals('inbox', $timed_good_email->getGroupName());
        $this->assertNull($timed_bad_email);
    }

    /*
     * Проверка ET12.1 для флага F14 чтоб была кнопка "Ответить"
     */

    public function testNewFlagsRules() {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(1, $user);

        FlagsService::setFlag($simulation, 'F14', 1);

        EventsManager::startEvent($simulation, 'ET12.1', false, false, 0);

        $result = EventsManager::getState($simulation, []);

        $this->assertEquals(3, count($result['events'][0]['data']));
    }

    /*
     * Проверяет ET12.2 для F14 что есть нужная реплика excel_id = 408
     */

    public function testNewFlagsRulesByDialogGet() {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(1, $user);
        $dialog = new DialogService();
        FlagsService::setFlag($simulation, 'F14', 1);
        $id = Replica::model()->findByAttributes(['code'=>'ET12.1', 'replica_number'=> 1, 'step_number'=> 1])->id;

        $result = $dialog->getDialog($simulation->id, $id, '9:00');

        $this->assertEquals('408', $result['events'][0]['data'][1]['excel_id']);
    }
}
