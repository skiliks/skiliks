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
    public function test_log_reply_all()
    {
        // $this->markTestSkipped();
        
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        $mgr = new EventsManager();
        $mail = new MailBoxService();
        $character = Characters::model()->findByAttributes(['code' => 9]);

        $message = $mail->sendMessage([
            'subject_id' => CommunicationTheme::model()->findByAttributes(['code' => 5, 'character_id' => $character->primaryKey, 'mail_prefix' => 're'])->primaryKey,
            'message_id' => MailTemplateModel::model()->findByAttributes(['code' => 'MS40'])->primaryKey,
            'receivers' => $character->primaryKey,
            'sender' => Characters::model()->findByAttributes(['code' => 1])->primaryKey,
            'copies' => implode(',',[
                Characters::model()->findByAttributes(['code' => 2])->primaryKey,
                Characters::model()->findByAttributes(['code' => 11])->primaryKey,
                Characters::model()->findByAttributes(['code' => 12])->primaryKey,
            ]),
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
        /** @var $mail_logs LogMail[] */
        $mail_logs = LogMail::model()->findAllByAttributes(['sim_id' => $simulation->primaryKey]);
        $this->assertEquals($mail_logs[0]->full_coincidence, 'MS40');
        foreach ($mail_logs as $log) {
            //print_r($log);
        }
        $this->assertEquals(count($mail_logs), 1);
        foreach ($mail_logs as $log) {
            printf("%8s\t%8s\t%10s\n",
                $log->start_time,
                $log->end_time,
                $log->full_coincidence ?: '(empty)'
            );
            /*$this->assertNotNull($log->end_time);*/
        }
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
        $this->assertEquals($activity_actions[2]->activityAction->activity_id, 'TM1');
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
    public function test_log_m8_forward()
    {
        // $this->markTestSkipped();
        
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
        // $this->markTestSkipped();
        
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
    
    public function testActivityLogAgregated() 
    {
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
        $logItem->save();
        
        // 2. mainMain 4 minutes, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:01:00';
        $logItem->end_time           = '09:04:00';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->save();
        
        // 3. mainMain 2 minutes, add to A_wait
        // Big (> speed*10 real seconds) but same as previouse action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:04:00';
        $logItem->end_time           = '09:06:00';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->save();
        
        // 4. plan plan 1 minute, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:06:00';
        $logItem->end_time           = '09:07:00';
        $logItem->activity_action_id = $planPlanWindowActivityAction->id;
        $logItem->save();
        
        // 5. mainMain 1 minute, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:07:00';
        $logItem->end_time           = '09:08:00';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->save();
        
        // 6. plan plan 10 minutes, add as T1.1
        // new Big activity must produce new agregated log record
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:08:00';
        $logItem->end_time           = '09:18:00';
        $logItem->activity_action_id = $planPlanWindowActivityAction->id;
        $logItem->save();
        
        // 7. mainMain 1 minute, add to T1.1
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:18:00';
        $logItem->end_time           = '09:19:00';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
        $logItem->save();        
        
        // 8. plan plan 1 minute, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:19:00';
        $logItem->end_time           = '09:19:24';
        $logItem->activity_action_id = $planPlanWindowActivityAction->id;
        $logItem->save();
        
        // 9. mainMain 1 minute, add to A_wait
        // Small (< speed*10 real seconds) action must be added to previouse action
        $logItem                     = new LogActivityAction();
        $logItem->sim_id             = $simulation->id;
        $logItem->start_time         = '09:19:24';
        $logItem->end_time           = '09:19:52';
        $logItem->activity_action_id = $mainMainWindowActivityAction->id;
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
