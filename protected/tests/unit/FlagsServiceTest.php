<?php

class FlagServiceTest extends CDbTestCase
{
    /**
     * Проверяет, устанавливаются ли флаги, при выборе определенной реплики
     */
    public function testDialogFlagSet()
    {
        //$this->markTestSkipped();

        $simulationService = new SimulationService();
        /** @var $user Users */
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulationService->simulationStart(2, $user);

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

        $simulationService = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulationService->simulationStart(2, $user);

        $senderId = Characters::model()->findByAttributes(['code' => Characters::HERO_ID])->primaryKey;
        $receiverId = Characters::model()->findByAttributes(['code' => '12'])->primaryKey;
        $msgParams = [
            'simId' => $simulation->id,
            'subject_id' => CommunicationTheme::model()->findByAttributes([
                'code'=>55, 'character_id' => $receiverId, 'mail_prefix'=>null])->primaryKey,
            'message_id' => 0,
            'receivers' => $receiverId,
            'group' => MailBoxModel::OUTBOX_FOLDER_ID,
            'sender' => $senderId,
            'time' => '11:00',
            'letterType' => null
        ];

        $mail = MailBoxService::sendMessage($msgParams);
        MailBoxService::updateMsCoincidence($mail->id, $simulation->id);

        $msgParams['subject_id'] = CommunicationTheme::model()->findByAttributes(['code'=>55, 'character_id' => $receiverId, 'mail_prefix'=>'rere'])->primaryKey;
        $mail = MailBoxService::sendMessage($msgParams);
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

        $simulationService = new SimulationService();
        /** @var $user Users */
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulationService->simulationStart(1, $user);
        //$dialog = new DialogService();
        // case 1

        $e = new EventsManager();
        $e->startEvent($simulation->id, 'S2', false, false, 0);

        /*$dialogs = Replica::model()->findAllByAttributes([
            'code'        => 'S2',
            'step_number' => 1
        ]);*/

        /*$ids = [];
        foreach ($dialogs as $dialog) {
            $ids[] = $dialog->excel_id;
        }*/
        $data = [];
        //case 1
        $result = $e->getState($simulation, []);
        //$result = $dialog->getDialog($simulation->id, '134', '12:00:00');
        foreach ($result['events'][0]['data'] as $replica) {
            if($replica['ch_from'] == 1) {
                $this->assertFalse(in_array($replica['excel_id'], $data));
                $data[] = $replica['excel_id'];
            }
        }
        // case 2


        FlagsService::setFlag($simulation->id, 'F1', 1);

        $e = new EventsManager();
        $e->startEvent($simulation->id, 'S2', true, true, 0);

       $result = $e->getState($simulation, []);
       foreach ($result['events'][0]['data'] as $replica) {
           if($replica['ch_from'] == 1) {
               $this->assertFalse(in_array($replica['excel_id'], $data));
               $data[] = $replica['excel_id'];
           }
        }
        //case3
        FlagsService::setFlag($simulation->id, 'F1', 0);
        FlagsService::setFlag($simulation->id, 'F2', 1);

        $e = new EventsManager();
        $e->startEvent($simulation->id, 'S2', true, true, 0);

        $result = $e->getState($simulation, []);
        foreach ($result['events'][0]['data'] as $replica) {
            if($replica['ch_from'] == 1) {
                $this->assertFalse(in_array($replica['excel_id'], $data));
                $data[] = $replica['excel_id'];
            }
        }
        //case 4
        FlagsService::setFlag($simulation->id, 'F2', 0);
        FlagsService::setFlag($simulation->id, 'F12', 1);

        $e = new EventsManager();
        $e->startEvent($simulation->id, 'S2', true, true, 0);

        $result = $e->getState($simulation, []);
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

        $simulationService = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulationService->simulationStart(1, $user);

        FlagsService::setFlag($simulation->id, 'F4', 0);

        // Case 1: block event
        $e = new EventsManager();
        $e->startEvent($simulation->id, 'ET1.3.1', false, false, 0);

        $result = $e->getState($simulation, []);

        $this->assertFalse(isset($result['events']));

        // Case 2: run event
        FlagsService::setFlag($simulation->id, 'F4', 1);

        $e = new EventsManager();
        $e->startEvent($simulation->id, 'ET1.3.1', false, false, 0);

        $result2 = $e->getState($simulation, []);

        $this->assertTrue(isset($result2['events']));
    }

    /**
     * Проверяет блокировку по флагу F14 собитыя E12
     * Тест эмулирует не просто запуск E12, а ответ на реплику в ЕТ12.3 которая приводит к Е12
     */
    public function testBlockDialogByGetDialog()
    {
        //$this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

        FlagsService::setFlag($simulation->id, 'F14', 0);

        $e = new EventsManager();
        $e->startEvent($simulation->id, 'ET12.3', false, false, 0);

        $r = $e->getState($simulation, []);

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

        $simulationService = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulationService->simulationStart(1, $user);

        // Case 1: block event
        $e = new EventsManager();
        $e->startEvent($simulation->id, 'E1.2.1', false, false, 0);

        $result = $e->getState($simulation, []);

        $this->assertFalse(isset($result['events']));

        // Case 2: run event
        FlagsService::setFlag($simulation->id, 'F3', 1);

        $e = new EventsManager();
        $e->startEvent($simulation->id, 'E1.2.1', false, false, 0);

        $result2 = $e->getState($simulation, []);

        $this->assertTrue(isset($result2['events']));
    }

    /**
     * Проверяет что письмо M31 отправляется по флагу F30, а M9 при флаге M16 — нет
     */
    public function testSendEmailAfterFlagSwitched()
    {
        ////$this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

        FlagsService::setFlag($simulation->id, 'F30', 1);
        FlagsService::setFlag($simulation->id, 'F16', 1);

        $e = new EventsManager();
        $result = $e->getState($simulation, []);
        $this->assertEquals(1, count($result['events']));
        $result = $e->getState($simulation, []);
        $this->assertEquals(0, $result['result']);

        /** @var $email MailBoxModel */
        $email = MailBoxModel::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M31'
        ]);
        /** @var $time_email MailBoxModel */
        $time_email = MailBoxModel::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M9'
        ]);
        $this->assertEquals('inbox', $email->getGroupName());
        $this->assertEquals('not received', $time_email->getGroupName());
        SimulationService::setSimulationClockTime($simulation, 16, 0);
        $i = 0;
        while (true) {
            $state = $e->getState($simulation, []);
            $i++;
            if ($state['result'] == 0) {
                break;
            }
        };
        /** @var $timed_good_email MailBoxModel */
        $timed_good_email = MailBoxModel::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M8'
        ]);
        /** @var $timed_bad_email MailBoxModel */
        $timed_bad_email = MailBoxModel::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M9'
        ]);
        $this->assertEquals('inbox', $timed_good_email->getGroupName());
        $this->assertEquals('not received', $timed_bad_email->getGroupName());
    }
}
