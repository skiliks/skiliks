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
    public function test_log_m8_forward()
    {
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);
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
            [1, 1, 'activated', 32400],
            [1, 1, 'deactivated', 32460],
            [10, 11, 'activated', 32460],
            [10, 11, 'deactivated', 32520],
            [10, 13, 'activated', 32520], # Send mail
            [10, 13, 'deactivated', 32580, ['mailId' => $message->primaryKey]],
            [1, 1, 'activated', 32940],
            [1, 1, 'deactivated', 33000],
        ]);

        $simulation_service->simulationStop($simulation->primaryKey);
        $logs = LogWindows::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $activity_actions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $mail_logs = LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        foreach ($mail_logs as $log) {
            //print_r($log);
        }
        $this->assertEquals(count($mail_logs), 1);

        $this->assertEquals(count($activity_actions), 4);
        foreach ($activity_actions as $log) {
            printf("%s\t%8s\t%10s\t%10s\n",
                $log->start_time,
                $log->end_time !== null ? $log->end_time : '(empty)',
                $log->activityAction->activity_id,
                $log->activityAction->mail !== null ? $log->activityAction->mail->code : '(empty)'
            );
            /*$this->assertNotNull($log->end_time);*/
        }
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

    public function test_log_activity()
    {
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);
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
            [1, 1, 'activated', 32400],
            [1, 1, 'deactivated', 32460],
            [10, 11, 'activated', 32460],
            [10, 11, 'deactivated', 32520],
            [10, 13, 'activated', 32520], # Send mail
            [10, 13, 'deactivated', 32580, ['mailId' => $message->primaryKey]],
            [10, 11, 'activated', 32580],
            [10, 11, 'deactivated', 32640],
            [10, 13, 'activated', 32640], # Send draft
            [10, 13, 'deactivated', 32700, ['mailId' => $draft_message->primaryKey]],
            [10, 11, 'activated', 32700],
            [10, 11, 'deactivated', 32760],
            [10, 13, 'activated', 32760], # Send draft
            [10, 13, 'deactivated', 32820, ['mailId' => $draft_message2->primaryKey]],
        ]);
        MailBoxService::sendDraft($simulation, $draft_message2);
        $mgr->processLogs($simulation, [
            [10, 11, 'activated', 32820],
            [10, 11, 'deactivated', 32880],
            [10, 13, 'activated', 32880], # Send draft
            [10, 13, 'deactivated', 32940, ['mailId' => $draft_message2->primaryKey]],
            [1, 1, 'activated', 32940],
            [1, 1, 'deactivated', 33000],
        ]);

        $simulation_service->simulationStop($simulation->primaryKey);
        $logs = LogWindows::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $activity_actions = LogActivityAction::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $mail_logs = LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        foreach ($mail_logs as $log) {
            //print_r($log);
        }
        $this->assertEquals(count($mail_logs), 4);

        $this->assertEquals(count($activity_actions), 10);
        foreach ($activity_actions as $log) {
            printf("%s\t%8s\t%10s\t%10s\n",
                $log->start_time,
                $log->end_time !== null ? $log->end_time : '(empty)',
                $log->activityAction->activity_id,
                $log->activityAction->mail !== null ? $log->activityAction->mail->code : '(empty)'
            );
            /*$this->assertNotNull($log->end_time);*/
        }
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
}
