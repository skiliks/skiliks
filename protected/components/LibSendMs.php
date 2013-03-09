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