<?php

/**
 * Description of MailBoxService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailBoxService
{
    /**
     *
     */
    const ACTION_NEW       = 'new';
    /**
     *
     */
    const ACTION_REPLY     = 'reply';
    /**
     *
     */
    const ACTION_REPLY_ALL = 'replyAll';
    /**
     *
     */
    const ACTION_FORWARD   = 'forward';
    /**
     *
     */
    const ACTION_EDIT      = 'edit';

    /**
     * Загрузка персонажей
     *
     * @param $simulation
     *
     * @return array
     */
    public static function getCharacters(Simulation $simulation, $copiesIds = null)
    {
        $resultCharacters = array();
        $criteria = new CDbCriteria();
        if ($copiesIds !== null) {
            $criteria->addInCondition('id', $copiesIds);
        }
        $charactersCollection = $simulation->game_type->getCharacters($criteria);

        foreach ($charactersCollection as $character) {
            $resultCharacters[$character->id] = $character->fio . ' <' . $character->email . '>';
        }

        return $resultCharacters;
    }

    /**
     * Crazy code!
     * 1. $params - options more stable, if there is a lot of them - object with options
     * 2. a lot of plain code - this is sure not OOP style
     *
     * Получение списка собщений
     *
     * @param $params
     * @internal param int $folderId
     * @internal param int $receiverId
     * @return array
     */
    public static function getMessages($params)
    {
        $folderId   = $params['folderId'];
        $simId      = $params['simId'];
        $simulation = Simulation::model()->findByPk($simId);

        /** @var MailBox[] $messages */
        $messages = MailBox::model()->findAllByAttributes([
            'sim_id'   => $params['simId'],
            'group_id' => $folderId,
        ]);

        $users = array();
        $list = array();
        $mailIds = array();
        $characters = self::getCharacters($simulation);
        foreach ($messages as $message) {
            $mailIds[] = (int)$message->id;
            $senderId = (int)$message->sender_id;
            $receiverId = (int)$message->receiver_id;
            $messageId = $message->message_id;
            $users[$senderId] = $senderId;
            $users[$receiverId] = $receiverId;

            $subject = $message->getFormattedTheme(); //$theme->getFormattedTheme();

            $readed = $message->readed;
            // Для черновиков и исходящих письма всегда прочитаны - fix issue 69
            if ($folderId == 2 || $folderId == 3) {
                $readed = 1;
            };

            // загрузим ка получателей {
            $receivers = MailRecipient::model()->findAllByAttributes(['mail_id' => $message->id]);
            $receiversCollection = [];

            if (count($receivers) == 0) {
                $receiversCollection[] = $characters[$simulation->game_type->getCharacter(['id' => $receiverId])->id];
            }

            foreach ($receivers as $receiver) {
                $receiversCollection[] = $characters[$receiver->receiver->id];
            }
            // загрузим ка получателей }

            // copy {
            $copies = MailCopy::model()->findAllByAttributes(['mail_id' => $message->id]);
            $copiesCollection = [];

            foreach ($copies as $copy) {
                $copiesCollection[] = $characters[$copy->receiver_id];
            }
            // copy }

            $item = array(
                'id'          => $message->id,
                'subject'     => $subject,
                'themeId'     => $message->theme_id,
                'text'        => $message->message ?: self::buildMessage($message->id),
                'template'    => (NULL !== $message->template) ? $message->template->code : NULL,
                'sentAt'      => GameTime::getDateTime($message->sent_at),
                'sender'      => $characters[$senderId],
                'receiver'    => implode(',', $receiversCollection),
                'copy'        => implode(',', $copiesCollection),
                'readed'      => $readed,
                'attachments' => 0,
                'folder'      => $folderId,
                'letterType'  => ('' === $message->letter_type ? 'new' : $message->letter_type),
                'mailPrefix' => $message->mail_prefix
            );

            if (!empty($messageId)) {
                $reply = MailBox::model()->findByPk($messageId);
                $item['reply'] = $reply->message;
            }

            if ($folderId == MailBox::FOLDER_DRAFTS_ID && $message->constructor_code !== 'TXT') {
                $item['phrases'] = self::getMessagePhrases($message);
                $item['phraseOrder'] = array_keys($item['phrases']);
            }

            $item['subjectSort'] = '';


            $list[(int)$message->id] = $item;
        }

        // Добавим информацию о вложениях

        if (count($mailIds) > 0) {
            $attachments = MailAttachment::model()->findAll([
                'condition' => sprintf( 'mail_id IN (%s)', implode(', ', $mailIds))
            ]);
            foreach ($attachments as $attachment) {
                if (isset($list[$attachment->mail_id])) {
                    $myDocument = MyDocument::model()->findByPk($attachment->file_id);
                    $list[$attachment->mail_id]['attachments']    = 1;
                    $list[$attachment->mail_id]['attachmentName'] = $myDocument->fileName;
                    $list[$attachment->mail_id]['attachmentId']   = $attachment->id;
                    $list[$attachment->mail_id]['attachmentFileId']   = $attachment->file_id;
                }
            }
        }

        // подготовка для сортировки на уровне php
        $receivers = array();
        foreach ($list as $key => $row) {
            $subjects[$key]  = $row['subjectSort'];
            $senders[$key]   = $row['sender'];
            $receivers[$key] = $row['receiver'];

        }

        return array_values($list);
    }

    /**
     * Полчаем фразы для симуляции по конструктору
     * @param Simulation $simulation
     * @param null|MailConstructor $constructor
     * @return array
     */
    public static function getMailPhrases(Simulation $simulation, $constructor = null)
    {
        if (null === $constructor) {
            $constructor = $simulation->game_type->getMailConstructor(['code' => 'B1']);
        }

        $phrases = MailPhrase::model()->findAllByAttributes(['constructor_id' => $constructor->getPrimaryKey()]);

        $list = [];
        foreach ($phrases as $model) {
            /* @var $model MailPhrase */
            $list[$model->id] = ['name' => $model->name, 'column_number'=>$model->column_number];
        }

        return $list;
    }

    /**
     * @param Simulation $simulation
     * @return array [',' , '.' , ':' ...]
     */
    public static function getSigns($simulation)
    {
        $constructor = $simulation->game_type->getMailConstructor(['code' => 'SYS']);
        $phrases = $constructor ? MailPhrase::model()->findAllByAttributes(['constructor_id' => $constructor->getPrimaryKey()]) : [];

        $list = [];
        foreach ($phrases as $model) {
            $list[$model->id] = ['name' => $model->name, 'column_number' => $model->column_number];
        }

        return $list;
    }

    /**
     * @param MailBox $mail
     * @return array
     */
    public static function getMessagePhrases(MailBox $mail)
    {
        $messages = MailMessage::model()->findAllByAttributes([
            'mail_id' => $mail->id
        ]);

        $result = [];
        foreach ($messages as $message) {
            $result[$message->phrase_id] = $message->phrase_id;
        }

        $phrases = MailPhrase::model()->findAllByAttributes([
            'id' => array_values($result)
        ]);

        foreach ($phrases as $phrase) {
            $result[$phrase->id] = $phrase->name;
        }

        return $result;
    }

    /**
     * Сборка сообщения
     * @param int $mailId
     * @return string
     */
    public static function buildMessage($mailId)
    {
        /** @var $mail MailBox */
        $mail = MailBox::model()->findByPk($mailId);
        if ($mail->constructor_code === 'TXT') {;
            return $mail->getMessageByReceiverAndTheme();
        }

        $phrases = self::getMessagePhrases($mail);
        return implode(' ', array_values($phrases));
    }

    /**
     * Получение списка тем
     * @param string $receivers
     */
    public static function getThemes(Simulation $simulation, $receivers, $mailPrefix, $parentThemeId)
    {
        $themes = [];
        if(empty($receivers)){
            return [];
        }
        $condition = [
            'character_to_id' => explode(',', $receivers)[0],
            'mail_prefix' => $mailPrefix === 'null'?null:$mailPrefix
        ];
        if(null !== $parentThemeId) {
            $condition['theme_id']  = $parentThemeId;
        }
        $outboxMailThemes = $simulation->game_type->getOutboxMailThemes($condition);

        /*  */
        foreach ($outboxMailThemes as $outboxMailTheme) {
            if(false === $outboxMailTheme->isBlockedByFlags($simulation) && false === $outboxMailTheme->themeIsUsed($simulation)) {
                $themes[(int)$outboxMailTheme->theme_id] = $outboxMailTheme->theme->text;
            }
        }

        if(count($outboxMailThemes) === 0 && $parentThemeId !== null) {
            $theme = $simulation->game_type->getTheme(['id'=>$parentThemeId]);
            $themes[(int)$theme->id] = $theme->getFormattedTheme($mailPrefix);
        }

        return $themes;
    }



    /**
     * Копирование сообщения из шаблонов писем в текущую симуляцию по коду
     * @param Simulation $simulation
     * @param string $code
     * @return bool|\CActiveRecord
     */
    public static function copyMessageFromTemplateByCode($simulation, $code)
    {
        // проверим а вдруг у нас уже есть такое сообщение
        $mailModel = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => $code,
        ]);
        if ($mailModel) return $mailModel; // сообщение уже есть у нас


        // проверим есть ли такоо сообщение вообще
        $mail = $simulation->game_type->getMailTemplate(['code' => $code]);
        if (!$mail) return false; // нечего копировать

        // копируем само письмо
        $connection = Yii::app()->db;
        $sql = "insert into mail_box
            (sim_id, template_id, group_id, sender_id, sent_at, receiver_id, message, code, type, letter_type, theme_id, mail_prefix)
            select :simId, id, group_id, sender_id, sent_at, receiver_id, message, code, type, '', theme_id, mail_prefix
            from mail_template
            where mail_template.code = :code AND scenario_id = :scenario_id";

        $command = $connection->createCommand($sql);
        $command->bindValue(":simId", $simulation->id, PDO::PARAM_INT);
        $command->bindValue(":code", $code);
        $scenarioId = $simulation->game_type->primaryKey;
        $command->bindValue(":scenario_id", $scenarioId);
        $command->execute();

        $mailModel = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => $code,
        ]);
        if (!$mailModel) return false; // что-то пошло не так - письмо не скопировалось в симуляцию

        // move from 5 (not send) to inbox (1)
        if (MailBox::FOLDER_NOT_RECEIVED_EMAILS_ID == $mailModel->group_id) {
            $mailModel->group_id = MailBox::FOLDER_INBOX_ID;
            $mailModel->save();
            $mailModel->refresh();
        }

        // copyMessageStructure {

        // выберем копии из шаблона
        $sql = "insert into mail_copies (mail_id, receiver_id) select :mailId, receiver_id from mail_copies_template where mail_id=:templateId";
        $command = $connection->createCommand($sql);
        $command->bindValue(":mailId", $mailModel->id, PDO::PARAM_INT);
        $command->bindValue(":templateId", $mailModel->template_id, PDO::PARAM_INT);
        $command->execute();

        // учтем множественных получателей
        $sql = "insert into mail_receivers (mail_id, receiver_id) select :mailId, receiver_id from mail_receivers_template where mail_id=:templateId";
        $command = $connection->createCommand($sql);
        $command->bindValue(":mailId", $mailModel->id, PDO::PARAM_INT);
        $command->bindValue(":templateId", $mailModel->template_id, PDO::PARAM_INT);
        $command->execute();

        // учесть вложение
        $sql = "select file_id from mail_attachments_template where mail_id = :mailId";

        $command = $connection->createCommand($sql);
        $command->bindValue(":mailId", $mailModel->template_id, PDO::PARAM_INT);
        $row = $command->queryRow();

        if (isset($row['file_id'])) {
            // определить file_id в симуляции
            $file = MyDocument::model()->findByAttributes([
                'sim_id'      => $simulation->id,
                'template_id' => (int)$row['file_id'],
            ]);

            if (!$file) {
                // документа еще нет в симуляции
                $fileId = MyDocumentsService::copyToSimulation($simulation->id, $row['file_id']);
            } else {
                $fileId = $file->id;
            }

            if ($fileId > 0) {
                $attachment = new MailAttachment();
                $attachment->mail_id = $mailModel->id;
                $attachment->file_id = $fileId;
                $attachment->insert();

                // проверим тип документа
                $fileTemplate = DocumentTemplate::model()->findByPk($row['file_id']);
                if ($fileTemplate->type != 'start') {
                    $file = MyDocument::model()->findByPk($fileId);
                    if ($file) {
                        $file->hidden = 1; // поскольку это аттач - спрячем его
                        $file->save();
                    }
                }
            }
        }

        self::addToQueue($simulation, $mailModel);


        // copyMessageStructure }

        $mailModel->refresh();

        return $mailModel;
    }

    /**
     * Копирование шаблонов писем в рамках заданной симуляции
     * @param int $simId
     */
    public static function initMailBoxEmails($simId)
    {
        $simulation = Simulation::model()->findByPk($simId);

        $connection = Yii::app()->db;
        $sql = "insert into mail_box
            (sim_id, template_id, group_id, sender_id, receiver_id, message, readed, code, sent_at, type, letter_type, theme_id, mail_prefix)
            select :simId, id, group_id, sender_id, receiver_id, message, 1, code, sent_at, type, '', theme_id, mail_prefix
            from mail_template  where group_id IN (1,3) AND scenario_id=:scenario_id";

        $command = $connection->createCommand($sql);
        $command->bindValue(":simId", $simId, PDO::PARAM_INT);
        $scenarioId = $simulation->game_type->getPrimaryKey();
        $command->bindValue(":scenario_id", $scenarioId, PDO::PARAM_INT);
        $command->execute();

        // теперь скопируем информацию о копиях писем
        $mailCollection = MailBox::model()->findAllByAttributes(['sim_id' => $simulation->id]);

        // prepare all doc templates
        $documentTemplates = [];
        foreach (DocumentTemplate::model()->findAll() as $documentTemplate) {
            $documentTemplates[$documentTemplate->id] = $documentTemplate;
        }

        // prepare all docs
        $myDocs = [];
        foreach (MyDocument::model()->findAllByAttributes(['sim_id' => $simId]) as $myDocument) {
            $myDocs[$myDocument->template_id] = $myDocument;
        }

        // init MyDocs for docTemplate in current simumation, if proper MyDoc isn`t exist
        $docIds = [];
        foreach (MailAttachmentTemplate::model()->findAll() as $mailAttachment) {
            if (false === isset($myDocs[$mailAttachment->file_id])) {
                $doc = new MyDocument();
                $doc->sim_id      = $simId;
                $doc->template_id = $documentTemplates[$mailAttachment->file_id]->id;
                $doc->fileName    = $documentTemplates[$mailAttachment->file_id]->fileName;
                $doc->save();

                $myDocs[$mailAttachment->file_id] = $doc;
            }

            $docIds[$mailAttachment->mail_id] = $myDocs[$mailAttachment->file_id]->id;
        }

        unset($myDocs, $documentTemplates);

        foreach ($mailCollection as $mail) {
            // plain SQL to make code faster
            $sql = '';

            // mail_copies
            $sql .= "insert into mail_copies (mail_id, receiver_id)".
                " select {$mail->id}, receiver_id from mail_copies_template".
                " where mail_id='{$mail->template_id}';";

            // mail mail_receivers
            $sql .= "insert into mail_receivers (mail_id, receiver_id)".
                " select {$mail->id}, receiver_id from mail_receivers_template".
                " where mail_id='{$mail->template_id}';";

            // mail mail_attachments
            if(isset($docIds[$mail->template_id])) {
                $sql .= "insert into mail_attachments (mail_id, file_id)".
                    " values ({$mail->id}, {$docIds[$mail->template_id]});";
            }

            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
            $command->execute();
        }
    }

    /**
     * @param int $id
     *
     * @return boolean
     */
    public static function markReaded($id)
    {
        $model = MailBox::model()->findByPk($id);
        if (NULL === $model) {
            return SimulationBaseController::STATUS_ERROR;
        }

        $model->readed = 1;
        $model->save();

        return SimulationBaseController::STATUS_SUCCESS;
    }

    /**
     * @param $simulation
     * @return array
     */
    public static function getFoldersUnreadCount($simulation)
    {
        $folders = [];

        $folders[MailBox::FOLDER_INBOX_ID]  = MailBox::model()->countByAttributes([
            'sim_id'   => $simulation->id,
            'group_id' => MailBox::FOLDER_INBOX_ID,
            'readed'   => 0
        ]);

        $folders[MailBox::FOLDER_DRAFTS_ID] = 0;

        $folders[MailBox::FOLDER_OUTBOX_ID] = 0;

        $folders[MailBox::FOLDER_TRASH_ID] = MailBox::model()->countByAttributes([
            'sim_id'   => $simulation->id,
            'group_id' => MailBox::FOLDER_TRASH_ID,
            'readed'   => 0
        ]);

        return $folders;
    }

    /**
     * @param MailBox $sendEmail
     */
    public static function updateRelatedEmailForByReplyToAttribute($sendEmail)
    {
        if ($sendEmail->letter_type == 'reply' OR $sendEmail->letter_type == 'replyAll') {
            if (!empty($sendEmail->message_id)) {
                $replyToEmail = MailBox::model()->findByPk($sendEmail->message_id);
                $replyToEmail->markReplied();
                $replyToEmail->update();
            } else {
                Yii::log(sprintf(
                    "Ошибка, не указан messageId для ответить или ответить всем. Отправленное письмо ID %s.",
                    $sendEmail->id
                ));
            }
        }
    }

    /**
     * @param integer $mailId, It must by ID of MS (sended from user) email
     * @param integer $simId
     * @return mixed
     */
    public static function updateMsCoincidence($mailId, $simId)
    {
        /* @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);

        $emailCoincidenceAnalyzer = new EmailCoincidenceAnalyzer();
        $emailCoincidenceAnalyzer->setUserEmail($mailId);
        $result = $emailCoincidenceAnalyzer->checkCoincidence();

        // update check MS email coincidence
        /** @var $log_mails LogMail[] */
        $log_mails = LogMail::model()->findAll(
            "`mail_id` = :mailId AND `end_time` > '00:00:00' AND `sim_id` = :simId ORDER BY `end_time`",
            [
                'mailId' => $mailId,
                'simId'  => $simId
            ]
        );
        /** @var $mail MailBox */
        $mail = MailBox::model()->findByPk($mailId);
        $mail->code = $result['result_code'];
        $mail->template_id = $result['result_template_id'];
        $mail->save();

        // switch flag if necessary {
        self::addToQueue($simulation, $mail);
        // switch flag if necessary }


        // update logs {
        foreach ($log_mails as $log_mail) {
            $log_mail->full_coincidence = $result['full'];
            $log_mail->part1_coincidence = $result['part1'];
            $log_mail->part2_coincidence = $result['part2'];
            $log_mail->is_coincidence = $result['has_concidence'];
            $log_mail->save();
        }
        // update logs }

        $simulationEmail = MailBox::model()->findByPk($mailId);

        if (null !== $simulationEmail) {
            $simulationEmail->code = $result['result_code'];
            $simulationEmail->template_id = $result['result_template_id'];
            $simulationEmail->coincidence_type = $result['result_type'];
            $simulationEmail->coincidence_mail_code = $result['result_code'];
            $simulationEmail->save();
        }

        return $result;
    }

    /**
     * Получаем список фраз или текст для письма
     * @param Simulation $simulation
     * @param $themeId
     * @param $characterId
     * @param $mailPrefix
     * @return array
     */
    public static function getPhrases(Simulation $simulation, $themeId, $characterToId, $mailPrefix = null)
    {
        $data = [];
        $message = '';
        $constructorCode = 'B1';
        $addData = [];

        /* @var $outbox_mail_theme OutboxMailTheme */
        $outbox_mail_theme = OutboxMailTheme::model()->findByAttributes([
            'character_to_id' => $characterToId,
            'theme_id'        => $themeId,
            'mail_prefix'     => $mailPrefix
        ]);

        if( null !== $outbox_mail_theme ) {
            if($outbox_mail_theme->mailConstructor !== null && $outbox_mail_theme->mailConstructor->code === 'TXT') {
                $mailTemplate = $simulation->game_type->getMailTemplate(['code' => $outbox_mail_theme->mail_code]);
                if (null === $mailTemplate) {
                    Yii::log('mailTemplate NULL for code '.$outbox_mail_theme->mail_code, CLogger::LEVEL_WARNING);
                    $message = '';
                } else {
                    $message = $mailTemplate->message;
                }
                $constructorCode = 'TXT';
            } else {
                if(null !== $outbox_mail_theme->mailConstructor){ $constructorCode = $outbox_mail_theme->mailConstructor->code; }
                $data = self::getMailPhrases($simulation, $outbox_mail_theme->mailConstructor);
                $addData = self::getSigns($simulation);
            }

        } else {
            $data = self::getMailPhrases($simulation);
            $addData = self::getSigns($simulation);
        }

        return [
            'constructorCode' => $constructorCode,
            'data'            => $data,
            'addData'         => $addData,
            'message'         => $message,
        ];
    }

    /**
     * This is code from controllers to send message
     * @togo: merge sendMessage to this method
     *
     * @param SendMailOptions $sendMailOptions
     * @return MailBox|null
     */
    public static function sendMessagePro($sendMailOptions)
    {
        if ($sendMailOptions->isReply() && $sendMailOptions->isValidMessageId()) {
            //Изменяем запись в бд: SK - 708
            $repliedEmail = MailBox::model()->findByPk($sendMailOptions->messageId);
            $repliedEmail->reply = true; //1 - значит что на сообщение отправлен ответ
            $repliedEmail->update();
        }

        assert($sendMailOptions->messageId !== null); // wtf ? ну а хули, пусть будет

        $letterType = $sendMailOptions->getLetterType();

        $receivers = $sendMailOptions->getRecipientsArray();

        $receiverId = (int)$receivers[0];

        if (null === $sendMailOptions->id) {
            $sendEmail = new MailBox();
        } else {
            $sendEmail = MailBox::model()->findByPk($sendMailOptions->id);
            MailCopy::model()->deleteAllByAttributes(['mail_id' => $sendMailOptions->id]);
            MailRecipient::model()->deleteAllByAttributes(['mail_id' => $sendMailOptions->id]);
            MailAttachment::model()->deleteAllByAttributes(['mail_id' => $sendMailOptions->id]);
            MailMessage::model()->deleteAllByAttributes(['mail_id'=>$sendEmail->id]);
        }

        $sendEmail->group_id    = $sendMailOptions->groupId;
        $sendEmail->sender_id   = $sendMailOptions->senderId;
        $sendEmail->theme_id    = $sendMailOptions->themeId;
        $sendEmail->theme       = Theme::model()->findByPk($sendMailOptions->themeId);
        $sendEmail->mail_prefix = $sendMailOptions->mailPrefix;
        $sendEmail->constructor_code = $sendMailOptions->constructorCode;
        $sendEmail->receiver_id = $receiverId;
        $sendEmail->sent_at = GameTime::setTimeToday($sendMailOptions->simulation, $sendMailOptions->time); //TODO: Время, проверить
        $sendEmail->readed = 0;

        if ($letterType != 'new') {
            $sendEmail->message_id = $sendMailOptions->messageId;
        }

        $sendEmail->sim_id = $sendMailOptions->simulation->id;
        $sendEmail->letter_type = $sendMailOptions->getLetterType() ?: '';

        if(null === $sendMailOptions->id) {
            $sendEmail->insert();
        }else{
            $sendEmail->update();
        }

        // сохранение копий
        if (null != $sendMailOptions->copies) {
            $receivers = explode(',', $sendMailOptions->copies);
            if ($receivers[count($receivers) - 1] == '') {
                unset($receivers[count($receivers) - 1]);
            }
            foreach ($receivers as $receiverId) {
                $model = new MailCopy();
                $model->mail_id = $sendEmail->id;

                $model->receiver_id = $receiverId;
                $model->insert();
            }
        }
        unset($receivers);

        // saveReceivers {
        $receivers =  $sendMailOptions->getRecipientsArray();
        if (count($receivers) != 0) {
            foreach ($receivers as $receiverId) {
                $model = new MailRecipient();
                $model->mail_id     = $sendEmail->id;
                $model->receiver_id = $receiverId;
                $model->insert();
            }
        }
        unset($receivers);
        // saveReceivers }

        // учтем аттачмена
        if (null !== $sendMailOptions->fileId) {
            MailAttachmentsService::refresh($sendEmail, $sendMailOptions->fileId);
        }

        // Сохранение фраз
        if (null != $sendMailOptions->phrases) {
            $phrases = explode(',', $sendMailOptions->phrases);
            foreach ($phrases as $phraseId) {
                if (null !== $phraseId && 0 != $phraseId && '' != $phraseId) {
                    $msg_model = new MailMessage();
                    $msg_model->mail_id = $sendEmail->id;
                    $msg_model->phrase_id = $phraseId;
                    $msg_model->insert();
                }
            }
        }

        $sendEmail->refresh();

        MailBoxService::updateMsCoincidence($sendEmail->id, $sendMailOptions->simulation->id);

        MailBoxService::updateRelatedEmailForByReplyToAttribute($sendEmail);

        return $sendEmail;
    }

    /**
     * @param SendMailOptions $sendMailOptions
     * @return \MailBox
     */
    public static function saveDraft($sendMailOptions)
    {
        $sendMailOptions->groupId   = MailBox::FOLDER_DRAFTS_ID;
        $sendMailOptions->senderId  = $sendMailOptions->simulation->game_type->getCharacter(['code' => Character::HERO_ID])->getPrimaryKey();

        $message = self::sendMessagePro($sendMailOptions);

        return $message;
    }

    /*
     * @param MailBox $email
     * @param int $folderId
     *
     * @return boolean
     */
    /**
     * @param $email
     * @param $folderId
     * @return bool
     */
    public static function moveToFolder($email, $folderId)
    {
        if (NULL === $email ||
            NULL === $folderId ||
            $email->group_id == MailBox::FOLDER_DRAFTS_ID ||
            $email->group_id == MailBox::FOLDER_OUTBOX_ID ||
            $folderId == MailBox::FOLDER_DRAFTS_ID ||
            $folderId == MailBox::FOLDER_OUTBOX_ID
        ) {
            return false;
        }

        $email->group_id = (int)$folderId;
        $email->save();

        return true;
    }

    /**
     * @param MailBox $message
     * @return mixed array
     */
    public static function getCopiesArray($message)
    {
        $copiesIds = array();
        $copies = array();

        $collection = MailRecipient::model()->findAllByAttributes(['mail_id' => $message->id]);
        $hero = $message->simulation->game_type->getCharacter(['code' => Character::HERO_ID]);

        foreach ($collection as $model) {
            // exclude our hero from copies
            if ($hero->id !== $model->receiver_id && $message->receiver_id !== $model->receiver_id) {
                $copiesIds[] = $model->receiver_id;
            }
        }

        if (count($copiesIds) > 0) {
            $copies = self::getCharacters($message->simulation, $copiesIds);
        }

        return array(
            implode(',', $copiesIds),
            implode(',', $copies)
        );
    }

    /**
     * @param MailBox $email
     *
     * @return mixed array
     */
    public static function getListTasksAvailableToPlanning($email)
    {
        // validation
        if (NULL === $email || NULL === $email->template_id || $email->isPlanned()) {
            return array();
        }

        $collection = MailTask::model()->findAllByAttributes([
            'mail_id' => $email->template_id
        ]);

        $tasks = array();
        foreach ($collection as $task) {
            $tasks[] = array(
                'id' => $task->id,
                'name' => $task->name,
                'duration' => $task->duration,
            );
        }

        return $tasks;
    }

    /**
     * @param $simulation
     * @param $email
     * @param $mailTask
     * @return null|Task
     */
    public static function addMailTaskToPlanner($simulation, $email, $mailTask)
    {
        if (NULL === $email || NULL === $mailTask || '' == $mailTask->name) {
            return NULL;
        }

        // Добавить новую задачу в план
        $task = new Task();
        $task->sim_id = $simulation->id;
        $task->title = $mailTask->name;
        $task->duration = $mailTask->duration;
        $task->category = $mailTask->category;
        $task->is_cant_be_moved = 0;
        $task->scenario_id = $simulation->game_type->primaryKey;
        $task->import_id = '';
        $task->save();

        $task->refresh();

        DayPlanService::addTask($simulation, $task->id, DayPlan::DAY_TODO);

        $email->plan = 1;
        $email->save();
        $email->refresh();

        return $task;
    }

    /**
     * @param Simulation $simulation
     * @param MailBox $email
     *
     * @return boolean
     */
    public static function sendDraft($simulation, $email)
    {
        assert($email);

        // update email folder {
        $email->group_id = MailBox::FOLDER_OUTBOX_ID;
        $email->save();

        // update email folder }

        MailBoxService::updateRelatedEmailForByReplyToAttribute($email);

        MailBoxService::updateMsCoincidence($email->id, $simulation->id);

        return SimulationBaseController::STATUS_SUCCESS;
    }

    /**
     * @param MailBox $message
     * @param string $action
     * @return array
     * @throws ErrorException
     */
    public static function getMessageData(MailBox $message, $action)
    {
        $result = [
            'result'      => 1,
            'themeId'   => $message->theme_id,
        ];
        $themePrefix = '';
        if ($action == self::ACTION_FORWARD) {
            $themePrefix = 'fwd';
            $result['phrases'] = self::getPhrases($message->simulation, $message->theme_id, null, null);
            $result['phrases']['previouseMessage'] = $message->message;
        } elseif ($action == self::ACTION_REPLY || $action == self::ACTION_REPLY_ALL) {
            $themePrefix = 're';
            $result['phrases'] = self::getPhrases($message->simulation, $message->theme_id, $message->receiver_id, $themePrefix.$message->mail_prefix);
            $result['phrases']['previouseMessage'] = $message->message;
        } elseif ($action == self::ACTION_EDIT) {
            //$condition['id'] = $message->subject_id;
        }
        $result['theme'] = $message->getFormattedTheme($themePrefix);

        if ($action == self::ACTION_FORWARD) {
            $result['parentThemeId'] = $message->theme_id;
            if (null !== $message->attachment) {
                $result['attachmentName']   = $message->attachment->myDocument->fileName;
                $result['attachmentId']     = $message->attachment->file_id;
            }
            // TODO: Check is this required
            if ($result['phrases']['constructorCode'] === 'TXT') {
                $result['text'] = $result['phrases']['message'];
            }
        }

        if ($action == self::ACTION_REPLY || $action == self::ACTION_REPLY_ALL) {
            $characters = self::getCharacters($message->simulation);
            $result['receiver'] = $characters[$message->sender_id];
            $result['receiver_id'] = $message->sender_id;
        }

        if ($action == self::ACTION_REPLY_ALL) {
            list($result['copiesIds'], $result['copies']) = self::getCopiesArray($message);
        }

        // Edit draft {
        if ($action == self::ACTION_EDIT) {
            $result['id'] = $message->id;

            $characters = self::getCharacters($message->simulation);
            $result['receiver'] = $characters[$message->receiver_id];
            $result['receiver_id'] = $message->receiver_id;

            if ($message->message_id) {
                $result['parentThemeId'] = $message->theme_id;
            }

            $result['copiesIds'] = array_map(function(MailCopy $copy) use ($characters) {
                return $copy->receiver_id;
            }, MailCopy::model()->findByAttributes(['mail_id' => $message->id]));
            $result['copies'] = self::getCharacters($message->simulation, $result['copiesIds']);

            $result['copiesIds'] = implode(',', $result['copiesIds']);
            $result['copies'] = implode(',', $result['copies']);

            $result['phrases']['previouseMessage'] = $message->message_id ? $message->message : '';

            if (null !== $message->attachment) {
                $result['attachmentName']   = $message->attachment->myDocument->fileName;
                $result['attachmentId']     = $message->attachment->file_id;
            }
        }
        $result['mailPrefix'] = $themePrefix.$message->mail_prefix;
        // Edit draft }

        return $result;
    }


    /**
     * @param Simulation $simulation
     * @param string $flag, like 'F1', 'F2'
     */
    public static function sendEmailsRelatedToFlag($simulation, $flag) {
        $mailFlags = $simulation->game_type->getFlagsRunMail([
            'flag_code' => $flag
        ]);

        foreach ($mailFlags as $mailFlag) {
            try {
                EventsManager::startEvent($simulation, $mailFlag->mail_code);
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_WARNING);
            }
        }
    }

    /**
     * @param Simulation $simulation
     * @param MailBox $mail
     */
    public static function addToQueue(Simulation $simulation, MailBox $mail){
        // switch flag when receive email
        if (NULL !== $mail->template && NULL !== $mail->template->flag_to_switch) {
            $flag = Flag::model()->findByAttributes(['code'=>$mail->template->flag_to_switch]);
            /* @var $flag Flag */
            if($flag->getDelay() === 0){
                FlagsService::setFlag($simulation, $mail->template->flag_to_switch, 1);
            }else{
                FlagsService::addFlagDelayAfterReplica($simulation, $flag);
            }

        }
    }

}