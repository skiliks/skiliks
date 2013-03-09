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
    use UnitLoggingTrait;
    /**
     * Проверяет работу ответа всем на письмо M1
     */
    public function test_log_reply_all()
    {
        //$this->markTestSkipped();
        
        $simulation_service = new SimulationService();
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::MODE_PROMO_ID, $user);
        $mgr = new EventsManager();

        $character = Character::model()->findByAttributes(['code' => 9]);

        $subject_id = CommunicationTheme::model()->findByAttributes([
            'code' => 5,
            'character_id' => $character->primaryKey,
            'mail_prefix' => 're',
            'theme_usage' => 'mail_outbox'
        ])->primaryKey;

        $copies = [
            Character::model()->findByAttributes(['code' => 2])->primaryKey,
            Character::model()->findByAttributes(['code' => 11])->primaryKey,
            Character::model()->findByAttributes(['code' => 12])->primaryKey,
        ];

        $options = new SendMailOptions();
        $options->phrases = '';
        $options->copies = implode(',', $copies);
        $options->messageId = MailTemplate::model()->findByAttributes(['code' => 'MS40'])->primaryKey;
        $options->subject_id = $subject_id;
        $options->setRecipientsArray($character->primaryKey);
        $options->senderId = Character::HERO_ID;
        $options->time = '11:00:00';
        $options->setLetterType('new');
        $options->groupId = MailBox::OUTBOX_FOLDER_ID;
        $options->simulation = $simulation;

        $message = MailBoxService::sendMessagePro($options);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray($character->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplate::model()->findByAttributes(['code' => 'MS40']);
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
        $sendMailOptions->messageId  = MailTemplate::model()->findByAttributes(['code' => 'MS52']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = implode(',', $copies);
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->subject_id    = CommunicationTheme::model()->findByAttributes([
            'code' => 6,
            'character_id' => $character->primaryKey,
            'theme_usage' => 'mail_outbox',
            'mail_prefix' => 're'])->primaryKey;
        $sendMailOptions->setLetterType('new');

        $draft_message2 = MailBoxService::saveDraft($sendMailOptions);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray($character->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplate::model()->findByAttributes(['code' => 'MS52']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = implode(',', $copies);
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->subject_id    = CommunicationTheme::model()->findByAttributes([
            'code' => 8,
            'character_id' => $character->primaryKey,
            'mail_prefix' => 'fwd'])->primaryKey;
        $sendMailOptions->setLetterType('new');

        $draft_message3 = MailBoxService::saveDraft($sendMailOptions);

        $logList = [];

        $this->appendSleep($logList, 60);
        $this->appendWindow($logList, 11);
        $this->appendNewMessage($logList, $message);
        $this->appendWindow($logList, 11);
        $this->appendNewMessage($logList, $draft_message);
        $this->appendWindow($logList, 11);
        $this->appendNewMessage($logList, $draft_message2);
        $this->appendWindow($logList, 11);


        $mgr->processLogs($simulation, $logList);

        MailBoxService::sendDraft($simulation, $draft_message2);
        MailBoxService::sendDraft($simulation, $draft_message3);

        $logList = [];
        $this->appendNewMessage($logList, $draft_message3);
        $this->appendWindow($logList, 11);
        $this->appendViewMessage($logList, $draft_message);
        $this->appendViewMessage($logList, $draft_message2);
        $this->appendSleep($logList, 60);
        $mgr->processLogs($simulation, $logList);

        $simulation_service->simulationStop($simulation);

        $logs = LogWindow::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
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
//        foreach ($activity_actions as $log) {
//            $log->dump();
//        }
        $this->assertEquals(count($activity_actions), 13);

//        echo "\n";


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
    public function testE2Logging()
    {
        //$this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::MODE_PROMO_ID, $user);

        $mgr = new EventsManager();
        $first_dialog = Replica::model()->findByAttributes(['excel_id' => 135]);
        $last_dialog = Replica::model()->findByAttributes(['excel_id' => 135]);
        $mgr->processLogs($simulation, [
            [20, 23, 'activated', 32460, ['dialogId' => $first_dialog->primaryKey], 'window_uid' => 1], # Send mail
            [20, 23, 'deactivated', 32520, ['dialogId' => $first_dialog->primaryKey, 'lastDialogId' => $last_dialog->primaryKey], 'window_uid' => 1], # Send mail
        ]);
        $simulation_service->simulationStop($simulation);
        //$log_windows = LogWindow::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);

//        foreach ($log_windows as $log) {
//            printf("%s\t%8s\t%s\n",
//                $log->start_time,
//                $log->end_time !== null ? $log->end_time : '(empty)',
//                $log->window
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }

        //$log_dialogs = LogDialog::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);

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
     * Проверяет, отображаются ли залогированные документы
     */
    public function testLogDocument()
    {
        $mgr = new EventsManager();
        $simulationService = new SimulationService();
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = $simulationService->simulationStart(Simulation::MODE_PROMO_ID, $user);
        $docTemplate = DocumentTemplate::model()->findByAttributes(['code' => 'D1']);
        $document  = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->primaryKey,
            'sim_id' => $simulation->primaryKey]);
        $mgr->processLogs($simulation, [
        [1, 1, 'activated', 32400, 'window_uid' => 1],
            [1, 1, 'deactivated', 32460, 'window_uid' => 1],
            [40, 41, 'activated', 32460, 'window_uid' => 2],
            [40, 41, 'deactivated', 32520, 'window_uid' => 2],
            [40, 42, 'activated', 32520, 'window_uid' => 3, ['fileId' => $document->primaryKey]], # Send mail
            [40, 42, 'deactivated', 32580, 'window_uid' => 3, ['fileId' => $document->primaryKey]],
            [40, 41, 'activated', 32580, 'window_uid' => 2],
            [40, 41, 'deactivated', 32640, 'window_uid' => 2],
            [40, 42, 'activated', 32640, 'window_uid' => 4, ['fileId' => $document->primaryKey]], # Send mail
            [40, 42, 'deactivated', 32700, 'window_uid' => 4, ['fileId' => $document->primaryKey]],
        ]);
        //$activityActions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        //array_map(function ($action) {$action->dump();}, $activityActions);
        $result = LogHelper::getLegActionsDetail(LogHelper::RETURN_DATA, $simulation);
        $this->assertEquals($result['data'][2]['leg_action'], 'D1');
    }

    /**
     * Проверяет, нормально ли работает поочередная отправка двух писем с сохранением в черновики
     */
    public function test_two_new_letters()
    {
        //$this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::MODE_PROMO_ID, $user);
        $mgr = new EventsManager();
        $character = Character::model()->findByAttributes(['code' => 9]);

        $subject_id = CommunicationTheme::model()->findByAttributes(['code' => 5, 'character_id' => $character->primaryKey, 'mail_prefix' => 're'])->primaryKey;
        $copies = [
            Character::model()->findByAttributes(['code' => 2])->primaryKey,
            Character::model()->findByAttributes(['code' => 11])->primaryKey,
            Character::model()->findByAttributes(['code' => 12])->primaryKey,
        ];
        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray($character->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplate::model()->findByAttributes(['code' => 'MS40']);
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
        $sendMailOptions->messageId  = MailTemplate::model()->findByAttributes(['code' => 'MS52']);
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
            [10, 11, 'deactivated', 32640, 'window_uid' => 2],
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

        $simulation_service->simulationStop($simulation);
        $logs = LogWindow::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
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
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::MODE_PROMO_ID, $user);
        $mgr = new EventsManager();
        $first_dialog = Replica::model()->findByAttributes(['excel_id' => 192]);
        $last_dialog = Replica::model()->findByAttributes(['excel_id' => 200]);
        $mgr->processLogs($simulation, [
            [20, 23, 'activated', 32460, ['dialogId' => $first_dialog->primaryKey], 'window_uid' => 1], # Send mail
            [20, 23, 'deactivated', 32520, ['dialogId' => $first_dialog->primaryKey, 'lastDialogId' => $last_dialog->primaryKey], 'window_uid' => 1], # Send mail
        ]);
        $simulation_service->simulationStop($simulation);
        $log_windows = LogWindow::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
//        foreach ($log_windows as $log) {
//            printf("%s\t%8s\t%s\n",
//                $log->start_time,
//                $log->end_time !== null ? $log->end_time : '(empty)',
//                $log->window
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }
        $log_dialogs = LogHelper::getDialogs(LogHelper::RETURN_DATA, $simulation);
//        foreach ($log_dialogs['data'] as $log) {
//            printf("%s\t%8s\t%5s\t%5d\n",
//                $log['start_time'],
//                $log['end_time'] !== null ? $log['end_time'] : '(empty)',
//                $log['code'],
//                $log['last_id']
//            );
//        }

    }

    /**
     * Правильность логирования пересылки письма М8 к Крутько
     */
    public function testLogM8Forward()
    {
        //$this->markTestSkipped();
        
        $simulation_service = new SimulationService();
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::MODE_PROMO_ID, $user);
        $mgr = new EventsManager();

        $krutko = Character::model()->findByAttributes(['code' => 4]);

        $options = new SendMailOptions();
        $options->phrases = '';
        $options->copies = '';
        $options->messageId = MailTemplate::model()->findByAttributes(['code' => 'M8'])->id;
        $options->subject_id = CommunicationTheme::model()->findByAttributes([
            'code' => 12,
            'character_id' => $krutko->id,
            'theme_usage' => 'mail_outbox'
        ])->id;
        $options->setRecipientsArray($krutko->id);
        $options->senderId = Character::HERO_ID;
        $options->time = '11:00:00';
        $options->setLetterType('new');
        $options->groupId = MailBox::OUTBOX_FOLDER_ID;
        $options->simulation = $simulation;

        $message = MailBoxService::sendMessagePro($options);

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

        $simulation_service->simulationStop($simulation);
        $logs = LogWindow::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
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
    public function testLogActivity()
    {
        //$this->markTestSkipped();
        
        $simulationService = new SimulationService();
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = $simulationService->simulationStart(Simulation::MODE_PROMO_ID, $user);
        $mgr = new EventsManager();

        $theme = CommunicationTheme::model()->findByAttributes([
            'code' => 38,
            'character_id' => Character::model()->findByAttributes(['code'=>20])->primaryKey,
            'mail_prefix' => 're',
            'theme_usage' => 'mail_outbox'
        ]);

        $options = new SendMailOptions();
        $options->phrases = '';
        $options->copies = '';
        $options->messageId = MailTemplate::model()->findByAttributes(['code' => 'M8'])->primaryKey;
        $options->subject_id = $theme->primaryKey;
        $options->setRecipientsArray(Character::model()->findByAttributes(['code' => 20])->primaryKey);
        $options->senderId = Character::HERO_ID;
        $options->time = '11:00:00';
        $options->setLetterType('new');
        $options->groupId = MailBox::OUTBOX_FOLDER_ID;
        $options->simulation = $simulation;

        $message = MailBoxService::sendMessagePro($options);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray(Character::model()->findByAttributes(['code' => 20])->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplate::model()->findByAttributes(['code' => 'M74']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = null;
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->subject_id    = $theme->primaryKey;
        $sendMailOptions->setLetterType('new');

        $draftMessage = MailBoxService::saveDraft($sendMailOptions);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray(Character::model()->findByAttributes(['code' => 20])->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplate::model()->findByAttributes(['code' => 'M74']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = null;
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->subject_id    = $theme->primaryKey;
        $sendMailOptions->setLetterType('new');
        $draftMessage2 = MailBoxService::saveDraft($sendMailOptions);
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
            [10, 13, 'deactivated', 32700, 'window_uid' => 5, ['mailId' => $draftMessage->primaryKey]],
            [10, 11, 'activated', 32700, 'window_uid' => 6],
            [10, 11, 'deactivated', 32760, 'window_uid' => 6],
            [10, 13, 'activated', 32760, 'window_uid' => 7], # Send draft
            [10, 13, 'deactivated', 32820, 'window_uid' => 7, ['mailId' => $draftMessage2->primaryKey]],
        ]);
        MailBoxService::sendDraft($simulation, $draftMessage2);
        $mgr->processLogs($simulation, [
            [10, 11, 'activated', 32820, 'window_uid' => 1],
            [10, 11, 'deactivated', 32880, 'window_uid' => 1],
            [10, 13, 'activated', 32880, 'window_uid' => 2], # Send draft
            [10, 13, 'deactivated', 32940, 'window_uid' => 2, ['mailId' => $draftMessage2->primaryKey]],
            [1, 1, 'activated', 32940, 'window_uid' => 3],
            [1, 1, 'deactivated', 33000, 'window_uid' => 3],
        ]);

        $simulationService->simulationStop($simulation);
        $logs = LogWindow::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $activityActions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $mailLogs = LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
//        foreach ($mail_logs as $log) {
//            //print_r($log);
//        }
        $this->assertEquals(count($mailLogs), 4);

        $this->assertEquals(count($activityActions), 10);
        array_map(function ($a) {$a->dump(); }, $activityActions);
        $this->assertEquals('TM73', $activityActions[2]->activityAction->activity_id);
        $this->assertEquals('A_already_used', $activityActions[6]->activityAction->activity_id);
        $this->assertEquals('A_already_used', $activityActions[8]->activityAction->activity_id);
        $time = new DateTime('9:00:00');
        foreach ($logs as $log) {
            $logStartTime = new DateTime($log->start_time);
            $logEndTime = new DateTime($log->end_time);
            $this->assertGreaterThanOrEqual($logStartTime, $logEndTime);
            $this->assertEquals($time, $logStartTime); # checks that there are no time holes
            $time = $logEndTime;
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
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::MODE_PROMO_ID, $user);
        
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

    /**
     * Проверяет что при работе с почтой в лог Universal попадают правильные имена (window.id) для Window и Subwindow
     */
    public function testLogMail()
    {
        ////$this->markTestSkipped();

        $simulation_service = new SimulationService();
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulation::MODE_PROMO_ID, $user);

        $logs = [];
        $logs[0][0] = 1;
        $logs[0][1] = 1;
        $logs[0][2]	= 'activated';
        $logs[0][3]	= 32400;
        $logs[0]['window_uid'] = 587;
        $logs[1][0]	= 1;
        $logs[1][1]	= 1;
        $logs[1][2]	= 'deactivated';
        $logs[1][3]	= 32552;
        $logs[1]['window_uid'] = 587;
        $logs[2][0]	= 10;
        $logs[2][1]	= 11;
        $logs[2][2]	= 'activated';
        $logs[2][3]	= 32553;
        $logs[2]['window_uid'] = 614;
        $logs[3][0]	= 10;
        $logs[3][1]	= 11;
        $logs[3][2]	= 'deactivated';
        $logs[3][3]	= 32559;
        $logs[3]['window_uid'] = 614;
        $logs[4][0]	= 10;
        $logs[4][1]	= 11;
        $logs[4][2] = 'activated';
        $logs[4][3]	= 32559;
        $logs[4]['window_uid'] = 615;


        $e = new EventsManager();

        $e->getState($simulation, $logs);

        $windowLogs = LogWindow::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        $this->assertEquals(1,  $windowLogs[0]->window, 'main screen');
        $this->assertEquals(11, $windowLogs[1]->window, 'mail screen 1');
        $this->assertEquals(11, $windowLogs[2]->window, 'mail screen 2');
    }
}
