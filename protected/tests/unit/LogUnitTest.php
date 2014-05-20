<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 03.02.13
 * Time: 14:06
 * To change this template use File | Settings | File Templates.
 */
class LogUnitTest extends CDbTestCase
{
    use UnitLoggingTrait;
    use UnitTestBaseTrait;
    /**
     * Проверяет работу ответа всем на письмо M1
     */
    public function testLogReplyAll()
    {
        //$this->markTestSkipped();

        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        MailBoxService::copyMessageFromTemplateByCode($simulation, 'M1');
        MailBoxService::copyMessageFromTemplateByCode($simulation, 'M2');

        /** @var MailBox $M1_mail */
        $M1_mail = MailBox::model()->findByAttributes(['sim_id' => $simulation->id, 'code' => 'M1']);
        $M2_mail = MailBox::model()->findByAttributes(['sim_id' => $simulation->id, 'code' => 'M2']);

        $character = $simulation->game_type->getCharacter(['code' => 9]);

        $theme_id = $M1_mail->theme_id;

        $copies = [
            $simulation->game_type->getCharacter(['code' => 2])->primaryKey,
            $simulation->game_type->getCharacter(['code' => 11])->primaryKey,
            $simulation->game_type->getCharacter(['code' => 12])->primaryKey,
        ];

        // Это MS40 с полным совпадением
        $options = new SendMailOptions($simulation);
        $options->phrases = '';
        $options->copies = implode(',', $copies);
        $options->messageId = $simulation->game_type->getMailTemplate(['code' => 'MS40'])->primaryKey;
        $options->themeId    = $theme_id;
        $options->mailPrefix = 're';
        $options->setRecipientsArray($character->primaryKey);
        $options->senderId = $simulation->game_type->getCharacter(['code' => Character::HERO_CODE])->getPrimaryKey();
        $options->time = '11:00:00';
        $options->setLetterType('new');
        $options->groupId = MailBox::FOLDER_OUTBOX_ID;
        $options->simulation = $simulation;

        $message = MailBoxService::sendMessagePro($options);

        $sendMailOptions1 = new SendMailOptions($simulation);
        $sendMailOptions1->setRecipientsArray($character->primaryKey);
        $sendMailOptions1->simulation = $simulation;
        $sendMailOptions1->messageId  = $simulation->game_type->getMailTemplate(['code' => 'MS40']);
        $sendMailOptions1->time = '11:00:00';
        $sendMailOptions1->copies     = null;
        $sendMailOptions1->phrases    = null;
        $sendMailOptions1->fileId     = 0;
        $sendMailOptions1->themeId    = $theme_id;
        $sendMailOptions1->mailPrefix = 're';
        $sendMailOptions1->setLetterType('new');

        $draft_message = MailBoxService::saveDraft($sendMailOptions1);

        // Это MS52 с частичным совпадением -  part 1
        $sendMailOptions2 = new SendMailOptions($simulation);
        $sendMailOptions2->setRecipientsArray($character->primaryKey);
        $sendMailOptions2->simulation = $simulation;
        $sendMailOptions2->messageId  = $simulation->game_type->getMailTemplate(['code' => 'MS52']);
        $sendMailOptions2->time       = '11:00:00';
        $sendMailOptions2->copies     = implode(',', $copies);
        $sendMailOptions2->phrases    = null;
        $sendMailOptions2->fileId     = 0;
        $sendMailOptions2->themeId    = $M2_mail->theme_id;
        $sendMailOptions2->mailPrefix = 're';

        $sendMailOptions2->setLetterType('new');

        $draft_message2 = MailBoxService::saveDraft($sendMailOptions2);

        // это не MS
        $sendMailOptions3 = new SendMailOptions($simulation);
        $sendMailOptions3->setRecipientsArray($character->primaryKey);
        $sendMailOptions3->simulation = $simulation;
        $sendMailOptions3->messageId  = $simulation->game_type->getMailTemplate(['code' => 'MS52']);
        $sendMailOptions3->time = '11:00:00';
        $sendMailOptions3->copies     = '';
        $sendMailOptions3->phrases    = null;
        $sendMailOptions3->fileId     = 0;
        $sendMailOptions3->themeId = $M1_mail->theme_id;
        $sendMailOptions3->mailPrefix = 'fwd';

        $sendMailOptions3->setLetterType('new');

        $draft_message3 = MailBoxService::saveDraft($sendMailOptions3);

        $logList = [];

        $this->appendSleep($logList, 60);
        $this->appendWindow($logList, 11);
        $this->appendNewMessage($logList, $message);
        $this->appendWindow($logList, 11);
        $this->appendNewMessage($logList, $draft_message);
        $this->appendWindow($logList, 11);
        $this->appendNewMessage($logList, $draft_message2);
        $this->appendWindow($logList, 11);

        EventsManager::processLogs($simulation, $logList);

        MailBoxService::sendDraft($simulation, $draft_message2);
        MailBoxService::sendDraft($simulation, $draft_message3);

        $logList = [];
        $this->appendNewMessage($logList, $draft_message3);
        $this->appendWindow($logList, 11);
        $this->appendViewMessage($logList, $draft_message);
        $this->appendViewMessage($logList, $draft_message2);
        $this->appendSleep($logList, 60);
        EventsManager::processLogs($simulation, $logList);

        SimulationService::simulationStop($simulation);

        // $logs = LogWindow::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        /** @var $activity_actions LogActivityAction[] */
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
//        }
//        die;
//        foreach ($activity_actions as $log) {
//            echo $log->dump();
//        }
//        echo "\n";

        $this->assertEquals($mail_logs[0]->full_coincidence, 'MS40');
        $this->assertEquals($mail_logs[2]->part1_coincidence, 'MS52');
        $this->assertEquals(count($activity_actions), 13);

        $this->assertEquals($activity_actions[2]->activityAction->activity->code, 'TM1');
        $this->assertEquals($activity_actions[8]->activityAction->activity->code, 'A_incorrect_sent');
        $this->assertEquals($activity_actions[10]->activityAction->activity->code, 'A_not_sent');
        $time = new DateTime('9:00:00');
//        foreach ($logs as $log) {
//            $log_start_time = new DateTime($log->start_time);
//            $log_end_time = new DateTime($log->end_time);
//            $this->assertGreaterThanOrEqual($log_start_time, $log_end_time);
//            $this->assertEquals($time, $log_start_time); # checks that there are no time holes
//            $time = $log_end_time;
//            $this->assertRegExp('/\d{2}:\d{2}:\d{2}/', $log->end_time);
//        }
    }

    /**
     * Проверка того, что E2 логируется одной записью, а не двумя
     */
    public function testE2Logging()
    {
        //$this->markTestSkipped();

        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);



        $first_dialog = Replica::model()->findByAttributes(['excel_id' => 135]);
        $last_dialog = Replica::model()->findByAttributes(['excel_id' => 135]);
        EventsManager::processLogs($simulation, [
            [20, 23, 'activated', 32460, ['dialogId' => $first_dialog->primaryKey], 'window_uid' => 1], # Send mail
            [20, 23, 'deactivated', 32520, ['dialogId' => $first_dialog->primaryKey, 'lastDialogId' => $last_dialog->primaryKey], 'window_uid' => 1], # Send mail
        ]);
        SimulationService::simulationStop($simulation);
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
        $this->initTestUserAsd();
        $user = $this->user;
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code'        => 'D1',
        ]);

        $document  = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->primaryKey,
            'sim_id'      => $simulation->primaryKey,
        ]);

        EventsManager::processLogs($simulation, [
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

        LogHelper::updateUniversalLog($simulation);
        $analyzer = new ActivityActionAnalyzer($simulation);
        $analyzer->run();

        $this->assertEquals($simulation->log_activity_actions[2]->activityAction->getAction()->getCode(), 'D1');
    }

    /**
     * Проверяет, нормально ли работает поочередная отправка двух писем с сохранением в черновики
     */
    public function test_two_new_letters()
    {
        //$this->markTestSkipped();

        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        /** @var MailTemplate $MS40 */
        $MS40 = MailTemplate::model()->findByAttributes(['code' => 'MS40']);

        /** @var MailTemplate $MS52 */
        $MS52 = MailTemplate::model()->findByAttributes(['code' => 'MS52']);

        $character = $simulation->game_type->getCharacter(['code' => 9]);

        $copies = [
            $simulation->game_type->getCharacter(['code' => 2])->primaryKey,
            $simulation->game_type->getCharacter(['code' => 11])->primaryKey,
            $simulation->game_type->getCharacter(['code' => 12])->primaryKey,
        ];
        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray($character->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = $MS40->id;
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = null;
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->themeId    = $MS40->theme->id;
        $sendMailOptions->mailPrefix = 're';
        $sendMailOptions->setLetterType('new');
        $draft_message = MailBoxService::saveDraft($sendMailOptions);
        
        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray($character->primaryKey);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = MailTemplate::model()->findByAttributes(['code' => 'MS52']);
        $sendMailOptions->time = '11:00:00';
        $sendMailOptions->copies     = implode(',', $copies);
        $sendMailOptions->phrases    = null;
        $sendMailOptions->fileId     = 0;
        $sendMailOptions->themeId    = $MS52->theme->id;
        $sendMailOptions->mailPrefix = 'fwd';
        $sendMailOptions->setLetterType('new');
        $draft_message2 = MailBoxService::saveDraft($sendMailOptions);

        EventsManager::processLogs($simulation, [
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

        SimulationService::simulationStop($simulation);
        // LogWindow::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $activity_actions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        /** @var $mail_logs LogMail[] */
        $mail_logs = LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $this->assertEquals(4, count($mail_logs));

        $this->assertEquals($activity_actions[2]->activityAction->activity->code, 'A_not_sent');
        $this->assertEquals('A_incorrect_sent', $activity_actions[4]->activityAction->activity->code);
        $this->assertEquals('A_not_sent', $activity_actions[6]->activityAction->activity->code);
        $this->assertEquals('A_incorrect_sent', $activity_actions[7]->activityAction->activity->code);

    }

    /**
     * Проверка того, что в E2.9 правильный last_dialog_id (только серверная часть)
     */
    public function testE2_9_Logging() {

        //$this->markTestSkipped();

        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $first_dialog = Replica::model()->findByAttributes(['excel_id' => 192]);
        $last_dialog = Replica::model()->findByAttributes(['excel_id' => 200]);
        EventsManager::processLogs($simulation, [
            [20, 23, 'activated', 32460, ['dialogId' => $first_dialog->primaryKey], 'window_uid' => 1], # Send mail
            [20, 23, 'deactivated', 32520, ['dialogId' => $first_dialog->primaryKey, 'lastDialogId' => $last_dialog->primaryKey], 'window_uid' => 1], # Send mail
        ]);
        SimulationService::simulationStop($simulation);
//        $log_windows = LogWindow::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
//        foreach ($log_windows as $log) {
//            printf("%s\t%8s\t%s\n",
//                $log->start_time,
//                $log->end_time !== null ? $log->end_time : '(empty)',
//                $log->window
//            );
//            /*$this->assertNotNull($log->end_time);*/
//        }
//        $log_dialogs = LogHelper::getDialogs(LogHelper::RETURN_DATA, $simulation);
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
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $krutko = Character::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code' => 4,
        ]);

        $options = new SendMailOptions($simulation);
        $options->phrases = '';
        $options->copies = '';

        /** @var MailTemplate $M8 */
        $M8 = MailTemplate::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'code'        => 'M8'
        ]);

        $options->messageId = $M8->id;

        $hero = $simulation->game_type->getCharacter(['code' => Character::HERO_CODE]);

        $options->setRecipientsArray($krutko->id);
        $options->senderId   = $hero->id;
        $options->themeId    = $M8->theme->id;
        $options->mailPrefix = 'fwd'.$M8->mail_prefix;
        $options->time       = '11:00:00';
        $options->setLetterType('new');
        $options->groupId    = MailBox::FOLDER_OUTBOX_ID;
        $options->simulation = $simulation;

        $message = MailBoxService::sendMessagePro($options);

        EventsManager::processLogs($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1],
            [1, 1, 'deactivated', 32460, 'window_uid' => 1],
            [10, 11, 'activated', 32460, 'window_uid' => 2],
            [10, 11, 'deactivated', 32520, 'window_uid' => 2],
            [10, 13, 'activated', 32520, 'window_uid' => 3], # Send mail
            [10, 13, 'deactivated', 32580, 'window_uid' => 3, ['mailId' => $message->primaryKey]],
            [1, 1, 'activated', 32580, 'window_uid' => 4],
            [1, 1, 'deactivated', 33000, 'window_uid' => 4],
        ]);

        SimulationService::simulationStop($simulation);

        $activity_actions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $mail_logs = LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);

        $this->assertEquals(count($mail_logs), 1);

        $this->assertEquals(count($activity_actions), 4);

        $this->assertEquals($activity_actions[2]->activityAction->activity->code, 'TM8');
        $time = new DateTime('9:00:00');
//        foreach ($logs as $log) {
//            $log_start_time = new DateTime($log->start_time);
//            $log_end_time = new DateTime($log->end_time);
//            $this->assertGreaterThanOrEqual($log_start_time, $log_end_time);
//            $this->assertEquals($time, $log_start_time); # checks that there are no time holes
//            $time = $log_end_time;
//            $this->assertRegExp('/\d{2}:\d{2}:\d{2}/', $log->end_time);
//        }

    }

    /**
     * Проверяет LegAction по которому можно написать много писем. (TM72)
     * Если написано письмо, которое завершает LegAction (MS67)
     * прочие письма будут засчитаны как (A_already_used) - MS128 в данном тесте
     */
    public function testLogActivity()
    {
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $M72 = $simulation->game_type->getMailTemplate(['code' => 'M72']); // ответом на него есть MS67

        // MS67 - send
        /** @var SendMailOptions $options */
        $sendMailOptions1 = new SendMailOptions($simulation);
        $sendMailOptions1->setRecipientsArray(
            $simulation->game_type->getCharacter(['code' => 3])->id
        );

        $hero = $simulation->game_type->getCharacter(['code' => Character::HERO_CODE]);

        $sendMailOptions1->phrases    = '';
        $sendMailOptions1->copies     = '';
        $sendMailOptions1->mailPrefix = 'fwd'.$M72->mail_prefix;
        $sendMailOptions1->themeId    = $M72->theme->id;
        $sendMailOptions1->senderId   = $hero->id;
        $sendMailOptions1->time       = '11:00:00';
        $sendMailOptions1->messageId  = $M72->id;
        $sendMailOptions1->groupId    = MailBox::FOLDER_OUTBOX_ID;
        $sendMailOptions1->simulation = $simulation;
        $sendMailOptions1->setLetterType('new');

        $message = MailBoxService::sendMessagePro($sendMailOptions1);

        // Сохраняем повторно письмо
        // MS128 - draft
        $sendMailOptions2 = new SendMailOptions($simulation);

        $sendMailOptions2->setRecipientsArray(
            $simulation->game_type->getCharacter(['code' => 4])->id
        );
        $sendMailOptions2->phrases    = '';
        $sendMailOptions2->copies     = '';
        $sendMailOptions2->mailPrefix = 'fwd'.$M72->mail_prefix;
        $sendMailOptions2->themeId    = $M72->theme->id;
        $sendMailOptions2->senderId   = $hero->id;
        $sendMailOptions2->time       = '11:00:00';
        $sendMailOptions2->messageId  = $M72->id;
        $sendMailOptions2->groupId    = MailBox::FOLDER_OUTBOX_ID;
        $sendMailOptions2->simulation = $simulation;
        $sendMailOptions2->setLetterType('new');

        $draftMessage = MailBoxService::saveDraft($sendMailOptions2);

        // Сохраняем повторно письмо
        $draftMessage2 = MailBoxService::saveDraft($sendMailOptions2);

        // --------------

        EventsManager::processLogs($simulation, [
            [1, 1, 'activated', 32400, 'window_uid' => 1],
            [1, 1, 'deactivated', 32460, 'window_uid' => 1],
            [10, 11, 'activated', 32460, 'window_uid' => 2],
            [10, 11, 'deactivated', 32520, 'window_uid' => 2],
            [10, 13, 'activated', 32520, 'window_uid' => 3], # Send mail - MS32
            [10, 13, 'deactivated', 32580, 'window_uid' => 3, ['mailId' => $message->primaryKey]],
            [10, 11, 'activated', 32580, 'window_uid' => 4],
            [10, 11, 'deactivated', 32640, 'window_uid' => 4],

            # Send draft - не важно, черновик не распознаётся, и не превратится в legAction
            [10, 13, 'activated', 32640, 'window_uid' => 5],

            [10, 13, 'deactivated', 32700, 'window_uid' => 5, ['mailId' => $draftMessage->primaryKey]],
            [10, 11, 'activated', 32700, 'window_uid' => 6],
            [10, 11, 'deactivated', 32760, 'window_uid' => 6],
            [10, 13, 'activated', 32760, 'window_uid' => 7], # Send draft
            [10, 13, 'deactivated', 32820, 'window_uid' => 7, ['mailId' => $draftMessage2->primaryKey]],
        ]);

        MailBoxService::sendDraft($simulation, $draftMessage2);

        EventsManager::processLogs($simulation, [
            [10, 11, 'activated', 32820, 'window_uid' => 1],
            [10, 11, 'deactivated', 32880, 'window_uid' => 1],

            # Send draft - не важно, черновик не распознаётся, и не превратится в legAction
            [10, 13, 'activated', 32880, 'window_uid' => 2],

            [10, 13, 'deactivated', 32940, 'window_uid' => 2, ['mailId' => $draftMessage2->primaryKey]],
            [1, 1, 'activated', 32940, 'window_uid' => 3],
            [1, 1, 'deactivated', 33000, 'window_uid' => 3],
        ]);

        SimulationService::simulationStop($simulation);

        $activityActions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $mailLogs = LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);

        $this->assertEquals(count($mailLogs), 4);
        $this->assertEquals(count($activityActions), 10);

//        foreach ($mailLogs as $log) {
//            echo ' - '.$log->mail->code . ' - '. $log->mail->theme->theme_code . "\n";
//        }
//        echo "-------------------------------\n";
//        foreach ($activityActions as $action) {
//            echo ' - ' . $action->activityAction->activity->code . "\n";
//        }

        array_map(function ($a) {$a->dump(); }, $activityActions);
        $this->assertEquals('TM72', $activityActions[2]->activityAction->activity->code);
        $this->assertEquals('A_already_used', $activityActions[6]->activityAction->activity->code);
        $this->assertEquals('A_already_used', $activityActions[8]->activityAction->activity->code);
    }

    /**
     * Проверяет правильность агрегирования (схлопывания) 
     * данных из log activity actions (detailed) в log activity actions (agregated)
     */
    public function testActivityLogAggregated()
    {
        //$this->markTestSkipped();

        // init simulation
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


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
        
        $mainMainWindowActivityAction = $simulation->game_type->getActivityAction([
            'window_id'    => $mainMainWindow->id,
            'activity_id'  => $simulation->game_type->getActivity(['code' => 'A_wait'])->getPrimaryKey()
        ]);
        
        $planPlanWindowActivityAction = $simulation->game_type->getActivityAction([
            'window_id'    => $planPlanWindow->id,
            'activity_id'  => $simulation->game_type->getActivity(['code' => 'T1.1'])->getPrimaryKey()
        ]);
        
        $mails = [];
        
        $docs = [];
        
        // 1. mainMain 1 minute, is A_wait
        // Initial action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:00:00';
        $logItem->end_time           = '09:00:39';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->window_uid         = 1;
        $logItem->save();
        
        // 2. mainMain 4 minutes, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:00:39';
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
        $logItem->end_time           = '09:06:30';
        $logItem->activity_action_id = $planPlanWindowActivityAction->id;
        $logItem->window_uid         = 4;
        $logItem->save();
        
        // 5. mainMain 1 minute, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:06:30';
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
        $logItem->end_time           = '09:18:10';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->window_uid         = 7;
        $logItem->save();        
        
        // 8. plan plan 1 minute, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:18:10';
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
        $logs = LogActivityActionAggregated::model()->findAll( 'sim_id = :sim_id',  [
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
        $this->markTestSkipped();

        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


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


        EventsManager::getState($simulation, $logs);

//        $windowLogs = LogWindow::model()->findAllByAttributes([
//            'sim_id' => $simulation->id
//        ]);
//
//        $this->assertEquals(1,  $windowLogs[0]->window, 'main screen');
//        $this->assertEquals(11, $windowLogs[1]->window, 'mail screen 1');
//        $this->assertEquals(11, $windowLogs[2]->window, 'mail screen 2');
    }
}
