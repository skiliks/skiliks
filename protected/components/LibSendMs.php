<?php
/**
 * Сервисный класс для облегчения отправки писем в юнит тестах
 */
class LibSendMs
{

    /**
     * Отправляет MS в игре
     * @param Simulation $simulation
     * @param string $msCode код исходящего письма
     * @param null $time время отправки
     * @param int $windowId тип окна
     * @param int $subWindowId подтип окна
     * @param null $windowUid уникльный id окна
     * @param int $duration задержка
     * @param bool $isDraft черновик
     * @param string $letterType
     * @return MailBox|null
     */
    public static function sendMsByCode(Simulation $simulation, $msCode,
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
    public static function sendNotMs(Simulation $simulation)
    {
        $theme = $simulation->game_type->getTheme(['code'=>'127']);
        $character = $simulation->game_type->getCharacter(['fio'=>'Мягков Ю.']);
        $OutboxMailTheme = $simulation->game_type->getOutboxMailTheme([
            'character_to_id' => $character->id,
            'theme_id' => $theme->id,
            'mail_prefix' => null
        ]);

        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray($character->id);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->themeId = $theme->id;
        $sendMailOptions->messageId  = '';
        $sendMailOptions->mailPrefix = $OutboxMailTheme->mail_prefix;
        $sendMailOptions->constructorCode = $OutboxMailTheme->mailConstructor->code;

        return MailBoxService::sendMessagePro($sendMailOptions);
    }


    /**
     * Оьправка MS писем
     * @param Simulation $simulation
     * @param $messageCode
     * @param bool $isDraft
     * @param string $letterType
     * @param string $time
     * @return MailBox|null
     */
    public static function sendMs(Simulation $simulation, $messageCode, $isDraft = false, $letterType = '', $time = '9:01')
    {
        $mailTemplate = $simulation->game_type->getMailTemplate(['code' => $messageCode]);

        $attachmentTemplates = $mailTemplate->attachments;

        // collect recipient ids string {
        $recipientsString = $mailTemplate->receiver_id;
        $recipients = MailTemplateRecipient::model()->findAllByAttributes([
            'mail_id' => $mailTemplate->id
        ]);

        foreach ($recipients as $recipient) {
            $recipientsString .= ','.$recipient->receiver_id;
        }
        // collect recipient ids string }

        // collect copy ids string {
        $copiesArray = [];
        $copies = MailTemplateCopy::model()->findAllByAttributes([
            'mail_id' => $mailTemplate->id
        ]);

        foreach ($copies as $copy) {
            $copiesArray[] = $copy->receiver_id;
        }
        // collect copy ids string }

        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray($recipientsString);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = $time;
        $sendMailOptions->copies     = implode(',', $copiesArray);
        $sendMailOptions->phrases    = '';
        $sendMailOptions->setLetterType($letterType);

        if ($attachmentTemplates) {
            $doc = MyDocument::model()->findByAttributes([
                'template_id' => $attachmentTemplates[0]->file->id,
                'sim_id'      => $simulation->primaryKey
            ]);
            $sendMailOptions->fileId     = $doc->primaryKey;
        }
        $outbox_theme = $simulation->game_type->getOutboxMailTheme([
            'character_to_id' => $mailTemplate->receiver_id,
            'theme_id' => $mailTemplate->theme_id,
            'mail_prefix' => null
        ]);
        if(null === $outbox_theme || $outbox_theme->mailConstructor === null) {
            $mailConstructor = 'B1';
        }else{
            $mailConstructor = $outbox_theme->mailConstructor->code;
        }
        $sendMailOptions->constructorCode = $mailConstructor;
        $sendMailOptions->themeId = $mailTemplate->theme->id;
        $sendMailOptions->messageId  = '';
        $sendMailOptions->mailPrefix = $mailTemplate->mail_prefix;
        if ($isDraft) {
            return MailBoxService::saveDraft($sendMailOptions);
        } else {
            return MailBoxService::sendMessagePro($sendMailOptions);
        }

    }

    /**
     * Отправка MS с учётам Parent'a
     * @param Simulation $simulation
     * @param string $ms
     * @param $time время
     * @param $win окно
     * @param $sub_win подокно
     * @param $uid уникальный id окна
     * @param $parent_id
     */
    public static function sendMsByCodeWithParent(Simulation $simulation, $ms, $time, $win, $sub_win, $uid, $parent_id)
    {
        self::sendMsByCode($simulation, $ms, $time, $win, $sub_win, $uid);
        $parent = MailBox::model()->findByAttributes(['coincidence_mail_code'=>$ms, 'sim_id'=>$simulation->id]);
        $parent->message_id = $parent_id;
        $parent->sent_at = '2012-10-04 '.gmdate('H:i:s', $time);
        $parent->save();
    }
}