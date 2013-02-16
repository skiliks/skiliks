<?php

class FlagServiceTest extends CDbTestCase
{
    /**
     * Проверяет, устанавливаются ли флаги, при выборе определенной реплики
     */
    public function testDialogFlagSet()
    {
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

        $result = $e->getState($simulation, []);

        foreach ($result['events'][0]['data'] as $replicaDataArray) {
            $this->assertTrue(in_array($replicaDataArray['id'], [134, 135, 136]));
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
        $this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

        // FlagsService::setFlag($simulation->id, 'F4', 1);

        $e = new EventsManager();
        $e->startEvent($simulation->id, 'ET1.3.1', false, false, 0);

        $result = $e->getState($simulation, []);

        $this->assertFalse(isset($result['events']));
    }

    /**
     * Проверяет что письмо M10 отправляется по флагу F14
     */
    public function testSendEmailAfterFladSwitched()
    {
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);

        FlagsService::setFlag($simulation->id, 'F14', 1);

        $email = MailBoxModel::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M10'
        ]);

        $e = new EventsManager();
        $result = $e->getState($simulation, []);

        //var_dump($email->group_id, $result);

        //$this->assertEquals('1', $email->group_id);
    }
}
