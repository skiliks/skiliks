<?php
/**
 * Сервисный класс для облегчения отправки писем в юнит тестах
 */
class LibSendMs
{
    /**
     * @param Simulation $simulation
     * @param string $msCode
     * @param integer $time, frontend time im seconds
     * @param integer $windowId
     * @param integer $subWindowId
     * @param integer $windowUid, better set-up value manually - even in test
     * @param integer $duration
     *
     * @return MailBox
     */
    public static function sendMsByCode($simulation, $msCode,
        $time = null, $windowId = 1,  $subWindowId = 1, $windowUid = null, $duration = 10)
    {
        if( preg_match('/^MS\d+/', $msCode)) {
                $email = self::sendMs($simulation, $msCode);
        } else {
            $email = self::sendNotMs($simulation);
        }

        // update logs, optional
        if (null !== $time && null !== $email) {

            if (NULL == $windowUid) {
                $windowUid = rand(1000,9999) + rand(100,999); // for test cases
            }

            $logs = [];
            $fakeUID = rand(1000,9999);

            $logs[] = [$windowId, $subWindowId, 'deactivated', $time, 'window_uid' => $windowUid];
            $logs[] = [10       , 13          ,  'activated' , $time, 'window_uid' => $fakeUID];

            // set write an email duration :) 10 game seconds
            $time = $time + $duration;
            $logs[] = [10       , 13          , 'deactivated', $time, 'window_uid' => $fakeUID, 4 => ['mailId' => $email->id]];
            $logs[] = [$windowId, $subWindowId, 'activated'  , $time, 'window_uid' => $windowUid];

            $event = new EventsManager();
            $event->processLogs($simulation, $logs);
        }
        // set-up logs }

        return $email;
    }

    /**
     * We haven`t and probably woudn`t have MS to "Неизвестная"
     * So all MS to "Неизвестная" are wrong
     *
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendNotMs($simulation)
    {
        $randomSubjectForCharacter40 = CommunicationTheme::model()->findByAttributes([
            'character_id' => 32
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('32'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $randomSubjectForCharacter40->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs10_w($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 2,
            'letter_number' => 'MS10'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D10'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('2'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs20_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 2,
            'letter_number' => 'MS20'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('2'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs21_w($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 4,
            'letter_number' => 'MS21'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D1'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('4'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs22_w($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 3,
            'letter_number' => 'MS22'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D1'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('3'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs23_w($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 3,
            'letter_number' => 'MS23'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D3'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('3'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs25_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 6,
            'letter_number' => 'MS25'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D4'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('6'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs28_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 11,
            'letter_number' => 'MS28'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D8'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('11'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

     /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs27_w($simulation)
    {
        $emailFromSysadmin = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M8');

        $subject = CommunicationTheme::model()->find(
            'text = :text AND letter_number = :letter_number',[
            'text'          => '!проблема с сервером!',
            'letter_number' => 'MS27'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('3'); // Трутнев
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = $emailFromSysadmin->id;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs29_w($simulation)
    {
        $subject = CommunicationTheme::model()->find(
            'text = :text AND letter_number = :letter_number',[
            'text'          => 'задача: бюджет производства прошлого года',
            'letter_number' => 'MS29'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('3'); // Трутнев
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:03';
        $sendMailOptions->messageId  = '';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs30_w($simulation)
    {
        $receiverId = Character::model()->findByAttributes(['code' => '12'])->primaryKey;

        $msgParams = [
            'simId' => $simulation->id,
            'subject_id' => CommunicationTheme::model()->findByAttributes([
                'code'=>55,
                'character_id' => $receiverId,
                'mail_prefix'=>null
            ])->primaryKey,
            'message_id' => 0,
            'receivers' => $receiverId,
            'group' => MailBox::OUTBOX_FOLDER_ID,
            'sender' => Character::HERO_ID,
            'time' => '11:00',
            'letterType' => null
        ];

        return MailBoxService::sendMessage($msgParams);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs32_w($simulation)
    {
        $receiverId = Character::model()->findByAttributes(['code' => '12'])->primaryKey;

        $msgParams = [
            'simId' => $simulation->id,
            'subject_id' => CommunicationTheme::model()->findByAttributes([
                'character_id' => $receiverId,
                'letter_number'=>'MS32'
            ])->primaryKey,
            'message_id' => 0,
            'receivers' => $receiverId,
            'group' => MailBox::OUTBOX_FOLDER_ID,
            'sender' => Character::HERO_ID,
            'time' => '11:00',
            'letterType' => null
        ];

        return MailBoxService::sendMessage($msgParams);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs35_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 2,
            'letter_number' => 'MS35'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D18'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('2'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs36_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 2,
            'letter_number' => 'MS36'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D19'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('2'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs37_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 4,
            'letter_number' => 'MS37'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('4'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs39_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 4,
            'letter_number' => 'MS39'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('4'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs40_r($simulation)
    {
        $mailService = new MailBoxService();

        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 9,
            'letter_number' => 'MS40'
        ]);

        return $mailService->sendMessage([
            'subject_id' => $subject->id,
            'message_id' => MailTemplate::model()->findByAttributes(['code' => 'MS40'])->primaryKey,
            'receivers'  => '9',
            'sender'     => Character::model()->findByAttributes(['code' => 1])->primaryKey,
            'copies'     => '1,11,12',
            'time'       => '11:00:00',
            'group'      => 3,
            'letterType' => 'new',
            'simId'      => $simulation->primaryKey
        ]);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs48_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 2,
            'letter_number' => 'MS48'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('2'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs49_w($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 9,
            'letter_number' => 'MS49'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('9'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs50_w($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 2,
            'letter_number' => 'MS50'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('2'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs51_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 7,
            'letter_number' => 'MS51'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('7'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs53_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 2,
            'letter_number' => 'MS53'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D20'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('2'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs54_w($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 3,
            'letter_number' => 'MS54'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('3'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs55_r($simulation)
    {
        $mail = new MailBoxService();

        return $mail->sendMessage([
            'subject_id' => CommunicationTheme::model()->findByAttributes(['code' => 71])->primaryKey,
            'message_id' => MailTemplate::model()->findByAttributes(['code' => 'MS55']),
            'receivers' => Character::model()->findByAttributes(['code' => 39])->primaryKey,
            'sender' => Character::model()->findByAttributes(['code' => 1])->primaryKey,
            'time' => '11:00:00',
            'group' => 3,
            'letterType' => 'new',
            'simId' => $simulation->primaryKey
        ]);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs57_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 4,
            'letter_number' => 'MS57'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('4'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs58_w($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 3,
            'letter_number' => 'MS58'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('3'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs60_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 12,
            'letter_number' => 'MS60'
        ]);

        $message = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M75');

        // user can reply to received email only
        $message->group_id = MailBox::INBOX_FOLDER_ID;
        $message->save();

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('12'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = $message->id;

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs61_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 22,
            'letter_number' => 'MS61'
        ]);

        $message = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M76');

        // user can reply to received email only
        $message->group_id = MailBox::INBOX_FOLDER_ID;
        $message->save();

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('22'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = $message->id;

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs69_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 12,
            'letter_number' => 'MS69'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D2'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('12'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs74_w($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 4,
            'letter_number' => 'MS74'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('4'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs76_w($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 12,
            'letter_number' => 'MS76'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('12'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     */
    public static function sendMs79_n($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 12,
            'letter_number' => 'MS76'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('12'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     *
     */
    public static function sendMs80_w($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  =>  6,
            'letter_number' => 'MS83'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D5'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $message = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M56');

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('6'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = $message->id;

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBox
     * @deprecated
     */
    public static function sendMs83_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 6,
            'letter_number' => 'MS83'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D5'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('6');
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param $simulation
     * @return MailBox|null
     * @deprecated
     */
    public static function sendMs45_r($simulation)
    {
        $subject = CommunicationTheme::model()->findByAttributes([
            'character_id'  => 12,
            'letter_number' => 'MS45'
        ]);

        $docTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D5'
        ]);

        $doc = MyDocument::model()->findByAttributes([
            'template_id' => $docTemplate->id,
            'sim_id'      => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('6'); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $doc->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * Sends MS
     * @param $simulation Simulation
     * @param string $messageCode
     * @param bool $isDraft
     * @return MailBox|null
     */
    public static function sendMs($simulation, $messageCode, $isDraft = false)
    {
        $mailTemplate = MailTemplate::model()->findByAttributes(['code' => $messageCode]);
        /** @var $subject CommunicationTheme */
        $subject = $mailTemplate->subject_obj;

        $attachmentTemplates = $mailTemplate->attachments;

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray($subject->character_id); // Неизвестная
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        if ($attachmentTemplates) {
            $doc = MyDocument::model()->findByAttributes([
                'template_id' => $attachmentTemplates[0]->file->id,
                'sim_id'      => $simulation->primaryKey
            ]);
            $sendMailOptions->fileId     = $doc->primaryKey;
        }
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->messageId  = '';
        if ($isDraft) {
            return MailBoxService::saveDraft($sendMailOptions);
        } else {
            return MailBoxService::sendMessagePro($sendMailOptions);
        }

    }
}