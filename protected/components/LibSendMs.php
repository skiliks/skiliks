<?php
/**
 * Сервисный класс для облегчения отправки писем в юнит тестах
 */
class LibSendMs
{
    /**
     * @param Simulation $simulation
     * @param string $msCode
     * @param integer $time, frontend time in seconds, 9:30 = 34200
     * @param integer $windowId
     * @param integer $subWindowId
     * @param integer $windowUid, better set-up value manually - even in test
     * @param integer $duration
     *
     * @return MailBox
     */
    public static function sendMsByCode($simulation, $msCode,
        $time = null, $windowId = 1,  $subWindowId = 1, $windowUid = null, $duration = 10, $isDraft = false, $letterType = '')
    {
        if( preg_match('/^MS\d+/', $msCode)) {
            $email = self::sendMs($simulation, $msCode, $isDraft, $letterType);
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

            EventsManager::processLogs($simulation, $logs);
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
        $randomSubjectForCharacter40 = $simulation->game_type->getCommunicationTheme([
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
    public static function sendMs($simulation, $messageCode, $isDraft = false, $letterType = '')
    {
        $mailTemplate = $simulation->game_type->getMailTemplate(['code' => $messageCode]);
        /** @var $subject CommunicationTheme */
        $subject = $mailTemplate->subject_obj;

        $attachmentTemplates = $mailTemplate->attachments;

        // collect recipient ids string {
        $recipientsString = $subject->character_id;
        $recipients = MailTemplateRecipient::model()->findAllByAttributes([
            'mail_id' => $mailTemplate->id
        ]);

        foreach ($recipients as $recipient) {
            $recipientsString .= ','.$recipient->receiver_id;
        }
        // collect recipient ids string }

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray($recipientsString);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->setLetterType($letterType);

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

    public static function sendMsByCodeWithParent($simulation, $ms, $time, $win, $sub_win, $uid, $parent_id) {
        self::sendMsByCode($simulation, $ms, $time, $win, $sub_win, $uid);
        $parent = MailBox::model()->findByAttributes(['coincidence_mail_code'=>$ms, 'sim_id'=>$simulation->id]);
        $parent->message_id = $parent_id;
        $parent->sent_at = '2012-10-04 '.gmdate('H:i:s', $time);
        $parent->save();
    }
}