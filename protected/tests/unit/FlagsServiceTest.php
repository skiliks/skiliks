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
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulationService->simulationStart(2, $user);

        $dialogService = new DialogService();

        $dialogService->getDialog(
            $simulation->id,
            Dialogs::model()->byExcelId(35)->find()->id,
            '11:00'
        );
        $dialogService->getDialog(
            $simulation->id,
            Dialogs::model()->byExcelId(50)->find()->id,
            '11:00'
        );
        $dialogService->getDialog(
            $simulation->id,
            Dialogs::model()->byExcelId(70)->find()->id,
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
        $msgParams = [
            'simId' => $simulation->id,
            'subject_id' => 10495,
            'message_id' => 0,
            'receivers' => '12',
            'group' => MailBoxModel::OUTBOX_FOLDER_ID,
            'sender' => $senderId,
            'time' => '11:00',
            'letterType' => null
        ];

        $mail = MailBoxService::sendMessage($msgParams);
        MailBoxService::updateMsCoincidence($mail->id, $simulation->id);

        $msgParams['subject_id'] = 10498;
        $mail = MailBoxService::sendMessage($msgParams);
        MailBoxService::updateMsCoincidence($mail->id, $simulation->id);

        $flags = FlagsService::getFlagsState($simulation);

        $this->assertEquals($flags['F30'], '1');
        $this->assertEquals($flags['F31'], '1');
    }

    /**
     * Проверяет что на фронтенд попадают только правильные реплики по диалогу S2
     * @todo: функционал не готов
     */
    public function testBlockReplica()
    {
        //$this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

        // case 1

        $e = new EventsManager();
        $e->startEvent($simulation->id, 'S2', false, false, 0);

        $dialogs = Dialogs::model()->findAllByAttributes([
            'code'        => 'S2',
            'step_number' => 1
        ]);

        $ids = [];
        foreach ($dialogs as $dialog) {
            $ids[] = $dialog->excel_id;
        }

        $result = $e->getState($simulation, []);

        foreach ($result['events'][0]['data'] as $replicaDataArray) {
            $this->assertTrue(in_array($replicaDataArray['excel_id'], $ids));
        }
        // case 2
        // @todo: finalize

//        FlagsService::setFlag($simulation->id, 'F1', 1);
//
//        $e = new EventsManager();
//        $e->startEvent($simulation->id, 'S2', true, true, 0);
//
//        $result = $e->getState($simulation, []);
//
//        foreach ($result['events'][0]['data'] as $replicaDataArray) {
//            //$this->assertTrue(in_array($replicaDataArray['id'], []));
//        }
    }

    /**
     * Проверяет что диалог прокируется если не выставлен флаг
     */
    public function testBlockDialog()
    {
        //$this->markTestSkipped(); // S

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

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
        $json = $dialog->getDialog($simulation->id, 433, '09:10:00');

        $this->assertEquals(0, count($json['events']));
    }

    /**
     * Проверяет блокировку по флагу F3 собитыя E1.2.1
     */
    public function testBlockDialogByPhone()
    {
        //$this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

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
        while ($e->getState($simulation, [])['result']);
        $timed_good_email = MailBoxModel::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M9'
        ]);
        $this->assertEquals('inbox', $timed_good_email->getGroupName());



    }
}
