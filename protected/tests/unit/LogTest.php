<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 03.02.13
 * Time: 14:06
 * To change this template use File | Settings | File Templates.
 */
class LogTest extends CDbTestCase
{
    /**
     * Проверяет работу ответа всем на письмо M1
     */
    public function test_log_reply_all()
    {
        //$this->markTestSkipped();
        
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        $mgr = new EventsManager();
        $mail = new MailBoxService();
        $character = Characters::model()->findByAttributes(['code' => 9]);

        $subject_id = CommunicationTheme::model()->findByAttributes(['code' => 5, 'character_id' => $character->primaryKey, 'mail_prefix' => 're'])->primaryKey;
        $copies = [
            Characters::model()->findByAttributes(['code' => 2])->primaryKey,
            Characters::model()->findByAttributes(['code' => 11])->primaryKey,
            Characters::model()->findByAttributes(['code' => 12])->primaryKey,
        ];
        $message = $mail->sendMessage([
            'subject_id' => $subject_id,
            'message_id' => MailTemplateModel::model()->findByAttributes(['code' => 'MS40'])->primaryKey,
            'receivers' => $character->primaryKey,
            'sender' => Characters::model()->findByAttributes(['code' => 1])->primaryKey,
            'copies' => implode(',', $copies),
            'time' => '11:00:00',
            'group' => 3,
            'letterType' => 'new',
            'simId' => $simulation->primaryKey
        ]);
        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray($character->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplateModel::model()->findByAttributes(['code' => 'MS40']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = null;
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->subject_id    = $subject_id;
        $sendMailOptions->setLetterType('new');

        $draft_message = MailBoxService::saveDraft($sendMailOptions);


        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray($character->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplateModel::model()->findByAttributes(['code' => 'MS52']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = implode(',', $copies);
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->subject_id    = CommunicationTheme::model()->findByAttributes(['code' => 6, 'character_id' => $character->primaryKey, 'mail_prefix' => 're'])->primaryKey;
        $sendMailOptions->setLetterType('new');

        $draft_message2 = MailBoxService::saveDraft($sendMailOptions);


        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray($character->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplateModel::model()->findByAttributes(['code' => 'MS52']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = implode(',', $copies);
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->subject_id    = CommunicationTheme::model()->findByAttributes(['code' => 8, 'character_id' => $character->primaryKey, 'mail_prefix' => 'fwd'])->primaryKey;
        $sendMailOptions->setLetterType('new');

        $draft_message3 = MailBoxService::saveDraft($sendMailOptions);


        $mgr->processLogs($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1],
            [1, 1, 'deactivated', 32460, 'window_uid' => 1],
            [10, 11, 'activated', 32460, 'window_uid' => 2],
            [10, 11, 'deactivated', 32520, 'window_uid' => 2],
            [10, 13, 'activated', 32520, 'window_uid' => 3], # Send mail
            [10, 13, 'deactivated', 32580, 'window_uid' => 3, ['mailId' => $message->primaryKey]],
            [10, 11, 'activated', 32580, 'window_uid' => 4 ],
            [10, 11, 'deactivated', 32640, 'window_uid' => 4 ],
            [10, 13, 'activated', 32640, 'window_uid' => 5 ], # Send draft
            [10, 13, 'deactivated', 32700, 'window_uid' => 5, ['mailId' => $draft_message->primaryKey]],
            [10, 11, 'activated', 32700, 'window_uid' => 6],
            [10, 11, 'deactivated', 32760, 'window_uid' => 6],
            [10, 13, 'activated', 32760, 'window_uid' => 7], # Send draft
            [10, 13, 'deactivated', 32790, 'window_uid' => 7, ['mailId' => $draft_message2->primaryKey]],
            [10, 11, 'activated', 32790, 'window_uid' => 8],
            [10, 11, 'deactivated', 32805, 'window_uid' => 8],
        ]);

        MailBoxService::sendDraft($simulation, $draft_message2);
        MailBoxService::sendDraft($simulation, $draft_message3);

        $mgr->processLogs($simulation, [
            [10, 13, 'activated', 32805, 'window_uid' => 11], # Send draft
            [10, 13, 'deactivated', 32820, 'window_uid' => 11, ['mailId' => $draft_message3->primaryKey]],
            [10, 11, 'activated', 32820, 'window_uid' => 12],
            [10, 11, 'deactivated', 32880, 'window_uid' => 12],
            [10, 11, 'activated', 32880, 'window_uid' => 13, ['mailId' => $draft_message->primaryKey]], # Send draft
            [10, 11, 'deactivated', 32910, 'window_uid' => 13, ['mailId' => $draft_message->primaryKey]],
            [10, 11, 'activated', 32910, 'window_uid' => 14, ['mailId' => $draft_message2->primaryKey]], # Send draft
            [10, 11, 'deactivated', 32940, 'window_uid' => 14, ['mailId' => $draft_message2->primaryKey]],
            [1, 1, 'activated', 32940, 'window_uid' => 15],
            [1, 1, 'deactivated', 33000, 'window_uid' => 15],
        ]);

        $simulation_service->simulationStop($simulation->primaryKey);

        $logs = LogWindows::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $activity_actions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);

        /** @var $mail_logs LogMail[] */
        $mail_logs = LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $this->assertEquals(6, count($mail_logs));

//        echo "\n";
//        foreach ($mail_logs as $i =>  $log) {
//            printf("%8s\t %8s\t%8s\t%6d\t%10s\n",
//                $i,
//                $log->start_time,
//                $log->end_time,
//                $log->mail->template_id,
//                $log->full_coincidence ?: '(empty)'
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }

        $this->assertEquals($mail_logs[0]->full_coincidence, 'MS40');
        $this->assertEquals($mail_logs[2]->part1_coincidence, 'MS52');
        $this->assertEquals(count($activity_actions), 13);

//        echo "\n";
//        foreach ($activity_actions as $i => $log) {
//            printf("%s\t%8s\t%8s\t%10s\t%10s\t%10s\n",
//                $i,
//                $log->start_time,
//                $log->end_time !== null ? $log->end_time : '(empty)',
//                $log->activityAction->leg_type,
//                $log->activityAction->activity_id,
//                $log->activityAction->mail !== null ? $log->activityAction->mail->code : '(empty)'
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }

        $this->assertEquals($activity_actions[2]->activityAction->activity_id, 'TM1');
        $this->assertEquals($activity_actions[8]->activityAction->activity_id, 'A_wait');
        $this->assertEquals($activity_actions[10]->activityAction->activity_id, 'A_not_sent');
        $log_display = LogHelper::getLegActionsDetail(LogHelper::RETURN_DATA, $simulation);
        $this->assertEquals(count($activity_actions), count($log_display['data']));
        $this->assertEquals($log_display['data'][8]['activity_id'], 'A_wait');
        $this->assertEquals($log_display['data'][10]['activity_id'], 'A_not_sent');
        $time = new DateTime('9:00:00');
        foreach ($logs as $log) {
            $log_start_time = new DateTime($log->start_time);
            $log_end_time = new DateTime($log->end_time);
            $this->assertGreaterThanOrEqual($log_start_time, $log_end_time);
            $this->assertEquals($time, $log_start_time); # checks that there are no time holes
            $time = $log_end_time;
            $this->assertRegExp('/\d{2}:\d{2}:\d{2}/', $log->end_time);
        }

    }

    /**
     * Проверка того, что E2 логируется одной записью, а не двумя
     */
    public function testE2Logging() {
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        $mgr = new EventsManager();
        $first_dialog = Dialogs::model()->findByAttributes(['excel_id' => 135]);
        $last_dialog = Dialogs::model()->findByAttributes(['excel_id' => 135]);
        $mgr->processLogs($simulation, [
            [20, 23, 'activated', 32460, ['dialogId' => $first_dialog->primaryKey], 'window_uid' => 1], # Send mail
            [20, 23, 'deactivated', 32520, ['dialogId' => $first_dialog->primaryKey, 'lastDialogId' => $last_dialog->primaryKey], 'window_uid' => 1], # Send mail
        ]);
        $simulation_service->simulationStop($simulation->primaryKey);
        $log_windows = LogWindows::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);

//        foreach ($log_windows as $log) {
//            printf("%s\t%8s\t%s\n",
//                $log->start_time,
//                $log->end_time !== null ? $log->end_time : '(empty)',
//                $log->window
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }

        $log_dialogs = LogDialogs::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);

//        foreach ($log_dialogs as $log) {
//            printf("%s\t%8s\t%5d\t%5d\n",
//                $log->start_time,
//                $log->end_time !== null ? $log->end_time : '(empty)',
//                $log->dialog_id,
//                $log->last_id
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }

        //$this->assertEquals(count($log_dialogs), 2);

        $activity_actions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
//        foreach ($activity_actions as $log) {
//            printf("%5d\t%s\t%8s\t%10s\t%10s\n",
//                $log->id,
//                $log->start_time,
//                $log->end_time !== null ? $log->end_time : '(empty)',
//                $log->activityAction->activity_id,
//                $log->activityAction->mail !== null ? $log->activityAction->mail->code : '(empty)'
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }
        $this->assertEquals(count($activity_actions), 1);

    }

    /**
     * Проверяет, нормально ли работает поочередная отправка двух писем с сохранением в черновики
     */
    public function test_two_new_letters()
    {
        // //$this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        $mgr = new EventsManager();
        $mail = new MailBoxService();
        $character = Characters::model()->findByAttributes(['code' => 9]);

        $subject_id = CommunicationTheme::model()->findByAttributes(['code' => 5, 'character_id' => $character->primaryKey, 'mail_prefix' => 're'])->primaryKey;
        $copies = [
            Characters::model()->findByAttributes(['code' => 2])->primaryKey,
            Characters::model()->findByAttributes(['code' => 11])->primaryKey,
            Characters::model()->findByAttributes(['code' => 12])->primaryKey,
        ];
        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray($character->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplateModel::model()->findByAttributes(['code' => 'MS40']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = null;
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->subject_id    = $subject_id;
        $sendMailOptions->setLetterType('new');
        $draft_message = MailBoxService::saveDraft($sendMailOptions);
        
        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray($character->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplateModel::model()->findByAttributes(['code' => 'MS52']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = implode(',', $copies);
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->subject_id    = CommunicationTheme::model()->findByAttributes(['code' => 6, 'character_id' => $character->primaryKey, 'mail_prefix' => 'fwd'])->primaryKey;
        $sendMailOptions->setLetterType('new');
        $draft_message2 = MailBoxService::saveDraft($sendMailOptions);

        $mgr->processLogs($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1],
            [1, 1, 'deactivated', 32460, 'window_uid' => 1],
            [10, 11, 'activated', 32460, 'window_uid' => 2],
            [10, 11, 'deactivated', 32580, 'window_uid' => 2],
            [10, 13, 'activated', 32640, 'window_uid' => 4], # Send draft
            [10, 13, 'deactivated', 32700, ['mailId' => $draft_message->primaryKey], 'window_uid' => 4],
            [10, 11, 'activated', 32700, 'window_uid' => 5],
            [10, 11, 'deactivated', 32760, 'window_uid' => 5],
            [10, 13, 'activated', 32760, 'window_uid' => 6], # Send draft
            [10, 13, 'deactivated', 32790, ['mailId' => $draft_message2->primaryKey], 'window_uid' => 6],
            [10, 11, 'activated', 32790, 'window_uid' => 7],
            [10, 11, 'deactivated', 32805, 'window_uid' => 7],
            [10, 11, 'activated', 32805, ['mailId' => $draft_message->primaryKey], 'window_uid' => 8], # Send draft
            [10, 11, 'deactivated', 32910, ['mailId' => $draft_message->primaryKey], 'window_uid' => 8],
            [10, 11, 'activated', 32910, ['mailId' => $draft_message2->primaryKey], 'window_uid' => 9], # Send draft
            [10, 11, 'deactivated', 32940, ['mailId' => $draft_message2->primaryKey], 'window_uid' => 9],
            [1, 1, 'activated', 32940, 'window_uid' => 10],
            [1, 1, 'deactivated', 33000, 'window_uid' => 10],
        ]);
        MailBoxService::sendDraft($simulation, $draft_message2);

        $simulation_service->simulationStop($simulation->primaryKey);
        $logs = LogWindows::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $activity_actions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        /** @var $mail_logs LogMail[] */
        $mail_logs = LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $this->assertEquals(4, count($mail_logs));
//        foreach ($activity_actions as $log) {
//            printf("%s\t%8s\t%10s\t%10s\t%10s\n",
//                $log->start_time,
//                $log->end_time !== null ? $log->end_time : '(empty)',
//                $log->activityAction->leg_type,
//                $log->activityAction->activity_id,
//                $log->activityAction->mail !== null ? $log->activityAction->mail->code : '(empty)'
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }
        $this->assertEquals($activity_actions[2]->activityAction->activity_id, 'A_not_sent');
        $this->assertEquals($activity_actions[4]->activityAction->activity_id, 'A_not_sent');
        $this->assertEquals($activity_actions[6]->activityAction->activity_id, 'A_not_sent');
        $this->assertEquals($activity_actions[7]->activityAction->activity_id, 'A_not_sent');

    }

    /**
     * Проверка того, что в E2.9 правильный last_dialog_id (только серверная часть)
     */
    public function testE2_9_Logging() {

        //$this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        $mgr = new EventsManager();
        $first_dialog = Dialogs::model()->findByAttributes(['excel_id' => 192]);
        $last_dialog = Dialogs::model()->findByAttributes(['excel_id' => 200]);
        $mgr->processLogs($simulation, [
            [20, 23, 'activated', 32460, ['dialogId' => $first_dialog->primaryKey], 'window_uid' => 1], # Send mail
            [20, 23, 'deactivated', 32520, ['dialogId' => $first_dialog->primaryKey, 'lastDialogId' => $last_dialog->primaryKey], 'window_uid' => 1], # Send mail
        ]);
        $simulation_service->simulationStop($simulation->primaryKey);
        $log_windows = LogWindows::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
//        foreach ($log_windows as $log) {
//            printf("%s\t%8s\t%s\n",
//                $log->start_time,
//                $log->end_time !== null ? $log->end_time : '(empty)',
//                $log->window
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }
        $log_dialogs = LogHelper::getDialogs(LogHelper::RETURN_DATA, $simulation);
        foreach ($log_dialogs['data'] as $log) {
            printf("%s\t%8s\t%5s\t%5d\n",
                $log['start_time'],
                $log['end_time'] !== null ? $log['end_time'] : '(empty)',
                $log['code'],
                $log['last_id']
            );
            /*$this->assertNotNull($log->end_time);*/
        }

    }

    /**
     * Правильность логирования пересылки письма М8 к Крутько
     */
    public function test_log_m8_forward()
    {
        //$this->markTestSkipped();
        
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        $mgr = new EventsManager();
        $mail = new MailBoxService();
        $krutko = Characters::model()->findByAttributes(['code' => 4]);

        $message = $mail->sendMessage([
            'subject_id' => CommunicationTheme::model()->findByAttributes(['code' => 12, 'character_id' => $krutko->primaryKey])->primaryKey,
            'message_id' => MailTemplateModel::model()->findByAttributes(['code' => 'M8']),
            'receivers' => $krutko->primaryKey,
            'sender' => Characters::model()->findByAttributes(['code' => 1])->primaryKey,
            'time' => '11:00:00',
            'group' => 3,
            'letterType' => 'new',
            'simId' => $simulation->primaryKey
        ]);
        $mgr->processLogs($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1],
            [1, 1, 'deactivated', 32460, 'window_uid' => 1],
            [10, 11, 'activated', 32460, 'window_uid' => 2],
            [10, 11, 'deactivated', 32520, 'window_uid' => 2],
            [10, 13, 'activated', 32520, 'window_uid' => 3], # Send mail
            [10, 13, 'deactivated', 32580, 'window_uid' => 3, ['mailId' => $message->primaryKey]],
            [1, 1, 'activated', 32580, 'window_uid' => 4],
            [1, 1, 'deactivated', 33000, 'window_uid' => 4],
        ]);

        $simulation_service->simulationStop($simulation->primaryKey);
        $logs = LogWindows::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $activity_actions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $mail_logs = LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
//        foreach ($mail_logs as $log) {
//            print_r($log->attributes);
//        }
        $this->assertEquals(count($mail_logs), 1);

        $this->assertEquals(count($activity_actions), 4);
//        foreach ($activity_actions as $log) {
//            printf("%s\t%8s\t%10s\t%10s\n",
//                $log->start_time,
//                $log->end_time !== null ? $log->end_time : '(empty)',
//                $log->activityAction->activity_id,
//                $log->activityAction->mail !== null ? $log->activityAction->mail->code : '(empty)'
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }
        $this->assertEquals($activity_actions[2]->activityAction->activity_id, 'TM8');
        $time = new DateTime('9:00:00');
        foreach ($logs as $log) {
            $log_start_time = new DateTime($log->start_time);
            $log_end_time = new DateTime($log->end_time);
            $this->assertGreaterThanOrEqual($log_start_time, $log_end_time);
            $this->assertEquals($time, $log_start_time); # checks that there are no time holes
            $time = $log_end_time;
            $this->assertRegExp('/\d{2}:\d{2}:\d{2}/', $log->end_time);
        }

    }

    /**
     *
     */
    public function test_log_activity()
    {
        //$this->markTestSkipped();
        
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        $mgr = new EventsManager();
        $mail = new MailBoxService();
        $message = $mail->sendMessage([
            'subject_id' => CommunicationTheme::model()->findByAttributes(['code' => 38])->primaryKey,
            'message_id' => MailTemplateModel::model()->findByAttributes(['code' => 'M74']),
            'receivers' => Characters::model()->findByAttributes(['code' => 20])->primaryKey,
            'sender' => Characters::model()->findByAttributes(['code' => 1])->primaryKey,
            'time' => '11:00:00',
            'group' => 3,
            'letterType' => 'new',
            'simId' => $simulation->primaryKey
        ]);
        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray(Characters::model()->findByAttributes(['code' => 20])->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplateModel::model()->findByAttributes(['code' => 'M74']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = null;
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->subject_id    = CommunicationTheme::model()->findByAttributes(['code' => 38])->primaryKey;
        $sendMailOptions->setLetterType('new');
        $draft_message = MailBoxService::saveDraft($sendMailOptions);
        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray(Characters::model()->findByAttributes(['code' => 20])->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplateModel::model()->findByAttributes(['code' => 'M74']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = null;
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->subject_id    = CommunicationTheme::model()->findByAttributes(['code' => 38])->primaryKey;
        $sendMailOptions->setLetterType('new');
        $draft_message2 = MailBoxService::saveDraft($sendMailOptions);
        $mgr->processLogs($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1],
            [1, 1, 'deactivated', 32460, 'window_uid' => 1],
            [10, 11, 'activated', 32460, 'window_uid' => 2],
            [10, 11, 'deactivated', 32520, 'window_uid' => 2],
            [10, 13, 'activated', 32520, 'window_uid' => 3], # Send mail
            [10, 13, 'deactivated', 32580, 'window_uid' => 3, ['mailId' => $message->primaryKey]],
            [10, 11, 'activated', 32580, 'window_uid' => 4],
            [10, 11, 'deactivated', 32640, 'window_uid' => 4],
            [10, 13, 'activated', 32640, 'window_uid' => 5], # Send draft
            [10, 13, 'deactivated', 32700, 'window_uid' => 5, ['mailId' => $draft_message->primaryKey]],
            [10, 11, 'activated', 32700, 'window_uid' => 6],
            [10, 11, 'deactivated', 32760, 'window_uid' => 6],
            [10, 13, 'activated', 32760, 'window_uid' => 7], # Send draft
            [10, 13, 'deactivated', 32820, 'window_uid' => 7, ['mailId' => $draft_message2->primaryKey]],
        ]);
        MailBoxService::sendDraft($simulation, $draft_message2);
        $mgr->processLogs($simulation, [
            [10, 11, 'activated', 32820, 'window_uid' => 1],
            [10, 11, 'deactivated', 32880, 'window_uid' => 1],
            [10, 13, 'activated', 32880, 'window_uid' => 2], # Send draft
            [10, 13, 'deactivated', 32940, 'window_uid' => 2, ['mailId' => $draft_message2->primaryKey]],
            [1, 1, 'activated', 32940, 'window_uid' => 3],
            [1, 1, 'deactivated', 33000, 'window_uid' => 3],
        ]);

        $simulation_service->simulationStop($simulation->primaryKey);
        $logs = LogWindows::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $activity_actions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $mail_logs = LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
//        foreach ($mail_logs as $log) {
//            //print_r($log);
//        }
        $this->assertEquals(count($mail_logs), 4);

        $this->assertEquals(count($activity_actions), 10);
//        foreach ($activity_actions as $log) {
//            printf("%s\t%8s\t%10s\t%10s\n",
//                $log->start_time,
//                $log->end_time !== null ? $log->end_time : '(empty)',
//                $log->activityAction->activity_id,
//                $log->activityAction->mail !== null ? $log->activityAction->mail->code : '(empty)'
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }
        $this->assertEquals($activity_actions[2]->activityAction->activity_id, 'TM73');
        $this->assertEquals($activity_actions[6]->activityAction->activity_id, 'TM73');
        $this->assertEquals($activity_actions[8]->activityAction->activity_id, 'TM73');
        $time = new DateTime('9:00:00');
        foreach ($logs as $log) {
            $log_start_time = new DateTime($log->start_time);
            $log_end_time = new DateTime($log->end_time);
            $this->assertGreaterThanOrEqual($log_start_time, $log_end_time);
            $this->assertEquals($time, $log_start_time); # checks that there are no time holes
            $time = $log_end_time;
            $this->assertRegExp('/\d{2}:\d{2}:\d{2}/', $log->end_time);
        }

    }

    /**
     * Проверяет правильность агрегирования (схлопывания) 
     * данных из log activity actions (detailed) в log activity actions (agregated)
     */
    public function testActivityLogAgregated() 
    {
        //$this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        // init LogActivityActions {
        
        $mainMainWindow = Window::model()->find([
            'condition' => ' type = :type AND subtype = :subtype ',
            'params'    => [
                'type'    => 'main screen',
                'subtype' => 'main screen'
            ]
        ]);
        
        $planPlanWindow = Window::model()->find([
            'condition' => ' type = :type AND subtype = :subtype ',
            'params'    => [
                'type'    => 'plan',
                'subtype' => 'plan'
            ]
        ]);
        
        $mainMainWindowActivityAction = ActivityAction::model()->find([
            'condition' => ' window_id = :window_id AND activity_id = :activity_id ',
            'params'    => [
                'window_id'    => $mainMainWindow->id,
                'activity_id'  => 'A_wait'
            ]
        ]);
        
        $planPlanWindowActivityAction = ActivityAction::model()->find([
            'condition' => ' window_id = :window_id AND activity_id = :activity_id ',
            'params'    => [
                'window_id'    => $planPlanWindow->id,
                'activity_id'  => 'T1.1'
            ]
        ]);
        
        $mails = [];
        
        $docs = [];
        
        // 1. mainMain 1 minute, is A_wait
        // Initial action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:00:00';
        $logItem->end_time           = '09:01:00';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->window_uid         = 1;
        $logItem->save();
        
        // 2. mainMain 4 minutes, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:01:00';
        $logItem->end_time           = '09:04:00';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->window_uid         = 2;
        $logItem->save();
        
        // 3. mainMain 2 minutes, add to A_wait
        // Big (> speed*10 real seconds) but same as previouse action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:04:00';
        $logItem->end_time           = '09:06:00';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->window_uid         = 3;
        $logItem->save();
        
        // 4. plan plan 1 minute, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:06:00';
        $logItem->end_time           = '09:07:00';
        $logItem->activity_action_id = $planPlanWindowActivityAction->id;
        $logItem->window_uid         = 4;
        $logItem->save();
        
        // 5. mainMain 1 minute, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:07:00';
        $logItem->end_time           = '09:08:00';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->window_uid         = 5;
        $logItem->save();
        
        // 6. plan plan 10 minutes, add as T1.1
        // new Big activity must produce new agregated log record
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:08:00';
        $logItem->end_time           = '09:18:00';
        $logItem->activity_action_id = $planPlanWindowActivityAction->id;
        $logItem->window_uid         = 6;
        $logItem->save();
        
        // 7. mainMain 1 minute, add to T1.1
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:18:00';
        $logItem->end_time           = '09:19:00';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->window_uid         = 7;
        $logItem->save();        
        
        // 8. plan plan 1 minute, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:19:00';
        $logItem->end_time           = '09:19:24';
        $logItem->activity_action_id = $planPlanWindowActivityAction->id;
        $logItem->window_uid         = 8;
        $logItem->save();
        
        // 9. mainMain 1 minute, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:19:24';
        $logItem->end_time           = '09:19:52';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->window_uid         = 9;
        $logItem->save();
        
        // init LogActivityActions }
        
        // make LogActivityAgregated:
        LogHelper::combineLogActivityAgregated($simulation);
        
        // get ActivityAgregated logs
        $logs = LogActivityActionAgregated::model()->findAll( 'sim_id = :sim_id',  [
            'sim_id' => $simulation->id
        ]);
        
        // asserts:
        $this->assertEquals(2, count($logs), 'Wrong logs number.');
        
        // 1. A_wait
        $this->assertEquals('09:00:00', $logs[0]->start_time, 'Wrong start_time.');
        $this->assertEquals('09:08:00', $logs[0]->end_time,   'Wrong end_time.');
        
        // 2. T1.1
        $this->assertEquals('09:08:00', $logs[1]->start_time, 'Wrong start_time.');
        $this->assertEquals('09:19:52', $logs[1]->end_time,   'Wrong end_time.');
    }


}
