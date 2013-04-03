<?php

/**
 * Description of MailBoxService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailBoxService
{
    /**
     * Загрузка персонажей
     *
     * @param $simulation
     *
     * @return array
     */
    public static function getCharacters(Simulation $simulation)
    {
        $resultCharacters = array();

        $charactersCollection = $simulation->game_type->getCharacters([]);

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

        $order = (isset($params['order'])) ? $params['order'] : false;
        if ($order == -1) {
            $order = false;
        }

        $orderField = false;
        if ($order == 'sender') $orderField = 'sender_id';
        if ($order == 'time') $orderField = 'sent_at';

        $orderType = (isset($params['orderType'])) ? $params['orderType'] : false;
        if ($orderType == 0) $orderType = 'ASC';
        else $orderType = 'DESC';

        $model = MailBox::model();
        $model->bySimulation($params['simId']);

        $model->byFolder($folderId);
        if ($orderField) $model->orderBy($orderField, $orderType);
        $messages = $model->findAll();

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
            /** @var $theme CommunicationTheme */
            $theme = CommunicationTheme::model()->byId($message->subject_id)->find();

            $subject = $theme->getFormattedTheme();

            $readed = $message->readed;
            // Для черновиков и исходящих письма всегда прочитаны - fix issue 69
            if ($folderId == 2 || $folderId == 3) {
                $readed = 1;
            };

            // загрузим ка получателей {
            $receivers = MailRecipient::model()->byMailId($message->id)->findAll();
            $receiversCollection = [];

            if (count($receivers) == 0) {
                $receiversCollection[] = $characters[$simulation->game_type->getCharacter(['id' => $receiverId])->id];
            }

            foreach ($receivers as $receiver) {
                $receiversCollection[] = $characters[$receiver->receiver->id];
            }
            // загрузим ка получателей }

            // copy {
            $copies = MailCopy::model()->byMailId($message->id)->findAll();
            $copiesCollection = [];

            foreach ($copies as $copy) {
                $copiesCollection[] = $characters[$copy->receiver_id];
            }
            // copy }

            $item = array(
                'id'          => $message->id,
                'subject'     => $subject,
                'text'        => $message->message ?: self::buildMessage($message->id),
                'template'    => (NULL !== $message->template) ? $message->template->code : NULL,
                'sentAt'      => GameTime::getDateTime($message->sent_at),
                'sender'      => $characters[$senderId],
                'receiver'    => implode(',', $receiversCollection),
                'copy'        => implode(',', $copiesCollection),
                'readed'      => $readed,
                'attachments' => 0
            );

            if (!empty($messageId)) {
                $reply = MailBox::model()->byId($messageId)->find();
                $item['reply'] = $reply->message;
            }

            $item['subjectSort'] = '';


            $list[(int)$message->id] = $item;
        }


        if ($orderType == 'ASC') {
            $orderFlag = SORT_ASC;
        } else {
            $orderFlag = SORT_DESC;
        }

        // Добавим информацию о вложениях
        if (count($mailIds) > 0) {
            $attachments = MailAttachment::model()->byMailIds($mailIds)->findAll();
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

        if ($order == 'subject') {
            array_multisort($subjects, $orderFlag, $list);
        }

        if ($order == 'sender') {
            array_multisort($senders, $orderFlag, $list);
        }

        if ($order == 'receiver') {
            array_multisort($receivers, $orderFlag, $list);
        }

        return array_values($list);
    }

    /**
     * Загрузка одиночного сообщения
     * @param int $id
     * @return array
     */
    public static function getMessage($id)
    {
        $email = MailBox::model()->byId($id)->find();
        if (null === $email) {
            return array();
        }

        // mark Readed
        $email->readed = 1;
        $email->save();
        $themes = CommunicationTheme::model()->byId($email->subject_id)->find();
        $subject = $themes->text;

        $message = array(
            'id' => $email->id,
            'subject' => $subject,
            'message' => $email->message,
            'sentAt' => GameTime::getDateTime($email->sent_at),
            'sender' => $email->sender_id,
            'receiver' => $email->receiver_id
        );
        $message_id = $email->message_id;

        // Получим всех персонажей
        $characters = self::getCharacters($email->simulation);

        // загрузим ка получателей
        $receivers = MailRecipient::model()->byMailId($id)->findAll();
        $receiversCollection = array();

        if (count($receivers) == 0)
            $receiversCollection[] = $characters[$message['receiver']];

        foreach ($receivers as $receiver) {
            $receiversCollection[] = $characters[$receiver->receiver_id];
        }
        $message['receiver'] = implode(',', $receiversCollection);

        // загрузим копии
        $copies = MailCopy::model()->byMailId($id)->findAll();
        $copiesCollection = array();
        foreach ($copies as $copy) {
            $copiesCollection[] = $characters[$copy->receiver_id];
        }
        $message['copies'] = implode(',', $copiesCollection);


        $message['sender'] = $characters[$message['sender']];

        // Собираем сообщение
        if ($message['message'] == '') {
            $message['message'] = self::buildMessage($email->id);
        }

        $message['attachments'] = MailAttachmentsService::get($email);

        if (!empty($message_id)) {
            $reply = MailBox::model()->byId($message_id)->find();
            $message['reply'] = $reply->message;
        }
        return $message;
    }

    /**
     * @param Simulation $simulation
     * @param integer $id, CommunicationTheme.id
     * @return array
     */
    public static function getMailPhrases(Simulation $simulation, $id = NULL)
    {
        $phrases = array();

        if (NULL !== $id) {
            // получить код набора фраз
            /** @var $communicationTheme CommunicationTheme */
            $communicationTheme = CommunicationTheme::model()->byId($id)->find();
            // Если у нас прописан какой-то конструктор
            if ($communicationTheme) {
                $constructorNumber = $communicationTheme->constructor_number;
                $constructor = $communicationTheme->game_type->getConstructor(['code' => $constructorNumber]);
                // получить фразы по коду
                $phrases = MailPhrase::model()->findAllByAttributes(['constructor_id' => $constructor->getPrimaryKey()]);

                $list = array();
                foreach ($phrases as $model) {
                    $list[$model->id] = $model->name;
                }
                return $list;
            }
        }

        // конструтор не прописан - вернем дефолтовый
        if (count($phrases) == 0) {
            $constructor = $simulation->game_type->getConstructor(['code' => 'B1']);
            $phrases = MailPhrase::model()->findAllByAttributes(['constructor_id' => $constructor->getPrimaryKey()]);
        };

        $list = array();

        foreach ($phrases as $model) {
            $list[$model->id] = $model->name;
        }

        return $list;
    }

    /**
     * @param Simulation $simulation
     * @return array [',' , '.' , ':' ...]
     */
    public static function getSigns($simulation)
    {
        $constructor = $simulation->game_type->getConstructor(['code' => 'SYS']);
        $phrases = MailPhrase::model()->findAllByAttributes(['constructor_id' => $constructor->getPrimaryKey()]);

        $list = array();
        foreach ($phrases as $model) {
            $list[$model->id] = $model->name;
        }

        return $list;
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
        $characterTheme = $mail->subject_obj;
        if ($characterTheme && $characterTheme->constructor_number == 'TXT') {
            // MailTemplate indexed by MySQL id insteda of out code, so $characterTheme->letter relation doesn`t work
            $mailTemplate = $mail->simulation->game_type->getMailTemplate(['code' => $characterTheme->letter_number]);
            return $mailTemplate->message;
        }

        $models = MailMessage::model()->byMail($mailId)->findAll();

        $phrases = array();
        foreach ($models as $model) {
            $phrases[] = $model->phrase_id;
        }

        // получение набора фраз
        $phrasesCollection = MailPhrase::model()->byIds($phrases)->findAll();

        $phrasesDictionary = array();
        foreach ($phrasesCollection as $phraseModel) {
            $phrasesDictionary[$phraseModel->id] = $phraseModel->name;
        }

        $collection = array();
        foreach ($phrases as $index => $phraseId) {
            $collection[] = $phrasesDictionary[$phraseId];
        }

        // склейка фраз
        return implode(' ', $collection);
    }

    /**
     * Получение списка тем
     * @param string $receivers
     */
    public static function getThemes($receivers, $parentSubjectId = null)
    {
        if(empty($receivers)){
            return [];
        }

        $receivers = explode(',', $receivers);
        if ($receivers[count($receivers) - 1] == ',') unset($receivers[count($receivers) - 1]);
        if ($receivers[count($receivers) - 1] == '') unset($receivers[count($receivers) - 1]);

        $themes = array();
        // загрузка тем по одному персонажу
        if ($parentSubjectId !== null) {
            $parentSubject = CommunicationTheme::model()->findByPk($parentSubjectId);
            
            $models = [];            
            $model = CommunicationTheme::model()->find(
                'text = :text AND character_id = :character_id AND mail_prefix = :mail_prefix AND (theme_usage = :outbox)', [
                'mail_prefix'  => $parentSubject->getPrefixForForward(), 
                'text'         => $parentSubject->text,
                'character_id' => $receivers[0],
                'outbox'       => CommunicationTheme::USAGE_OUTBOX
            ]);
            if (NULL !== $model) {
                $models[] = $model;
            }
        } else {
            // this is NEW mail
            $models = CommunicationTheme::model()->findAll(
                'character_id = :character_id AND mail_prefix IS NULL AND mail = 1 AND theme_usage = :outbox ', [
                'character_id' => $receivers[0],
                'outbox'   => CommunicationTheme::USAGE_OUTBOX
            ]);
        }

        foreach ($models as $model) {
            $themes[(int)$model->id] = $model->getFormattedTheme();
        }

        return $themes;
    }

    /**
     * Копирование сообщения из шаблонов писем в текущую симуляцию по коду
     * @param Simulation $simulation
     * @param type $code
     * @return bool|\CActiveRecord
     */
    public static function copyMessageFromTemplateByCode($simulation, $code)
    {
        // проверим а вдруг у нас уже есть такое сообщение
        $mailModel = MailBox::model()->byCode($code)->bySimulation($simulation->id)->find();
        if ($mailModel) return $mailModel; // сообщение уже есть у нас


        // проверим есть ли такоо сообщение вообще
        $mail = $simulation->game_type->getMailTemplate(['code' => $code]);
        if (!$mail) return false; // нечего копировать

        // копируем само письмо
        $connection = Yii::app()->db;
        $sql = "insert into mail_box
            (sim_id, template_id, group_id, sender_id, sent_at, receiver_id, message, subject_id, code, type, letter_type)
            select :simId, id, group_id, sender_id, sent_at, receiver_id, message, subject_id, code, type, ''
            from mail_template
            where mail_template.code = :code AND scenario_id = :scenario_id";

        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simulation->id, PDO::PARAM_INT);
        $command->bindParam(":code", $code);
        $scenarioId = $simulation->game_type->primaryKey;
        $command->bindParam(":scenario_id", $scenarioId);
        $command->execute();

        $mailModel = MailBox::model()->byCode($code)->bySimulation($simulation->id)->find();
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
        $command->bindParam(":mailId", $mailModel->id, PDO::PARAM_INT);
        $command->bindParam(":templateId", $mailModel->template_id, PDO::PARAM_INT);
        $command->execute();

        // учтем множественных получателей
        $sql = "insert into mail_receivers (mail_id, receiver_id) select :mailId, receiver_id from mail_receivers_template where mail_id=:templateId";
        $command = $connection->createCommand($sql);
        $command->bindParam(":mailId", $mailModel->id, PDO::PARAM_INT);
        $command->bindParam(":templateId", $mailModel->template_id, PDO::PARAM_INT);
        $command->execute();

        // учесть вложение
        $sql = "select file_id from mail_attachments_template where mail_id = :mailId";

        $command = $connection->createCommand($sql);
        $command->bindParam(":mailId", $mailModel->template_id, PDO::PARAM_INT);
        $row = $command->queryRow();

        if (isset($row['file_id'])) {
            // определить file_id в симуляции
            $file = MyDocument::model()->bySimulation($simulation->id)->byTemplateId((int)$row['file_id'])->find();
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
                $fileTemplate = DocumentTemplate::model()->byId($row['file_id'])->find();
                if ($fileTemplate->type != 'start') {
                    $file = MyDocument::model()->byId($fileId)->find();
                    if ($file) {
                        $file->hidden = 1; // поскольку это аттач - спрячем его
                        $file->save();
                    }
                }
            }
        }

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
        $profiler = new SimpleProfiler(false);
        $profiler->startTimer();    
        $profiler->render('r1: ');
        
        $connection = Yii::app()->db;
        $sql = "insert into mail_box
            (sim_id, template_id, group_id, sender_id, receiver_id, message, readed, subject_id, code, sent_at, type, letter_type)
            select :simId, id, group_id, sender_id, receiver_id, message, 1, subject_id, code, sent_at, type, ''
            from mail_template  where group_id IN (1,3) AND scenario_id=:scenario_id";
        $profiler->render('r2: ');

        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $scenarioId = $simulation->game_type->getPrimaryKey();
        $command->bindParam(":scenario_id", $scenarioId, PDO::PARAM_INT);
        $command->execute();

        $profiler->render('r3: ');
        // теперь скопируем информацию о копиях писем
        $mailCollection = MailBox::model()->bySimulation($simId)->findAll();
        $profiler->render('r4: ');

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
        $profiler->render('r5: '); // 5
    }

    /**
     * @param int $id
     *
     * @return boolean
     */
    public static function markReaded($id)
    {
        $model = MailBox::model()->byId($id)->find();
        if (NULL === $model) {
            return false;
        }

        $model->readed = 1;
        $model->save();
    }

    /**
     * @param int $id
     *
     * @return boolean
     */
    public static function markPlanned($id)
    {
        $model = MailBox::model()->byId($id)->find();
        if (NULL === $model) {
            return false;
        }

        $model->plan = 1;
        $model->save();
    }

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
                $replyToEmail = MailBox::model()
                    ->byId($sendEmail->message_id)
                    ->find();
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
        $simulation = Simulation::model()->findByPk($simId);

        $emailCoincidenceAnalyzer = new EmailCoincidenceAnalyzer();
        $emailCoincidenceAnalyzer->setUserEmail($mailId);
        $result = $emailCoincidenceAnalyzer->checkCoincidence();

        // update check MS email coincidence
        /** @var $log_mails LogMail[] */
        $log_mails = LogMail::model()->findAll(
            "`mail_id` = :mailId AND `end_time` > '00:00:00' AND `sim_id` = :simId ORDER BY `window` DESC, `id` DESC",
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
        // @1229
        if (NULL !== $mail->template && NULL !== $mail->template->flag_to_switch) {
            FlagsService::setFlag($simulation, $mail->template->flag_to_switch, 1);
        }
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
     * @todo: add some comments for this magic code
     *
     * @param integer $characterThemeId
     * @param integer $forwardLetterCharacterThemesId
     *
     * @return mixed array
     */
    public static function getPhrases($characterThemeId, $forwardLetterCharacterThemesId, $simulation)
    {
        $data = array();
        $addData = array();
        $message = '';

        // for forwarded letters
        if ((int)$characterThemeId == 0 && (int)$forwardLetterCharacterThemesId != 0) {
            $characterThemeId = $forwardLetterCharacterThemesId;
        }

        if ((int)$characterThemeId == 0) {
            $data = self::getMailPhrases($simulation);
            $addData = self::getSigns($simulation);
        }

        $characterTheme = CommunicationTheme::model()->findByPk($characterThemeId);

        if (NULL !== $characterTheme &&
            'TXT' === $characterTheme->constructor_number
        ) {
            // MailTemplate indexed by MySQL id insteda of out code, so $characterTheme->letter relation doesn`t work
            $mailTemplate = $simulation->game_type->getMailTemplate(['code' => $characterTheme->letter_number]);
            $message = $mailTemplate->message;
        } else {
            $data = self::getMailPhrases($simulation, $characterThemeId);
            $addData = self::getSigns($simulation);
        }

        return array(
            'data' => $data,
            'addData' => $addData,
            'message' => $message,
        );
    }

    /**
     * This is code from controllers to send message
     * @togo: merge sendMEssage to this method
     *
     * @param SendMailOptions $sendMailOptions
     * @return MailBox|null
     */
    public static function sendMessagePro($sendMailOptions)
    {
        if ($sendMailOptions->isReply() && $sendMailOptions->isValidMessageId()) {
            //Изменяем запись в бд: SK - 708
            $repliedEmail = MailBox::model()->byId($sendMailOptions->messageId)->find();
            $repliedEmail->reply = true; //1 - значит что на сообщение отправлен ответ
            $repliedEmail->update();
        }

        assert($sendMailOptions->messageId !== null); // wtf ? ну а хули, пусть будет

        $letterType = $sendMailOptions->getLetterType();

        $receivers = $sendMailOptions->getRecipientsArray();

        $receiverId = (int)$receivers[0];

        $sendEmail = new MailBox();
        $sendEmail->group_id = $sendMailOptions->groupId;
        $sendEmail->sender_id = $sendMailOptions->senderId;
        $sendEmail->subject_id = $sendMailOptions->subject_id;
        $sendEmail->receiver_id = $receiverId;
        $sendEmail->sent_at = GameTime::setTimeToday($sendMailOptions->time); //TODO: Время, проверить
        $sendEmail->readed = 0;

        if ($letterType != 'new') {
            $sendEmail->message_id = $sendMailOptions->messageId;
        }

        $sendEmail->sim_id = $sendMailOptions->simulation->id;
        $sendEmail->letter_type = $sendMailOptions->getLetterType() ?: '';

        $sendEmail->insert();

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
            if ($receivers[count($receivers) - 1] == '') {
                unset($receivers[count($receivers) - 1]);
            }

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

        $sendEmail->refresh();

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
     * @param Simulation $simulation
     * @param MailBox $messageToReply
     * @param CommunicationTheme $characterThemeModel
     * @return type
     */
    public static function getPhrasesDataForReply($messageToReply, $characterThemeModel)
    {
        // validation
        if (NULL === $messageToReply ||
            MailBox::FOLDER_DRAFTS_ID == $messageToReply->group_id ||
            MailBox::FOLDER_OUTBOX_ID == $messageToReply->group_id
        ) {
            return array();
        }
        ;

        // init default responce
        $result = array(
               'message'          => '',
            'data'             => self::getMailPhrases($messageToReply->simulation),
            'previouseMessage' => $messageToReply->message,
            'addData'          => self::getSigns($messageToReply->simulation)
        );

        if ($characterThemeModel) {
            $characterThemeId = $characterThemeModel->id;
            $mailTemplate = $characterThemeModel->getMailTemplate();
            if ($characterThemeModel->constructor_number === 'TXT') {
                $result['message'] = (NULL === $mailTemplate) ? '' : $mailTemplate->message;
                $result['data']    = [];
                $result['addData'] = [];
            } else {
                $result['data'] = self::getMailPhrases($messageToReply->simulation, $characterThemeId);
            }
        }
        // get phrases }

        return $result;
    }


    /**
     * @param MailBox $messageToReply
     * @return mixed array
     */
    public static function getCopiesArrayForReplyAll($messageToReply)
    {
        $copiesIds = array();
        $copies = array();

        $collection = MailRecipient::model()->byMailId($messageToReply->id)->findAll();

        foreach ($collection as $model) {
            // exclude our hero from copies
            if ($messageToReply->simulation->game_type->getCharacter(['code' => Character::HERO_ID])->primaryKey !== $model->receiver_id) {
                $copiesIds[] = $model->receiver_id;
            }
        }

        if (count($copiesIds) > 0) {
            $copies = self::getCharacters($copiesIds);
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

        $task->id = $task->id;

        TodoService::add($simulation, $task);

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

        return true;
    }

    /**
     * @params CommunicationTheme $messageToReply
     */
    public static function getSubjectForRepryEmail($messageToReply)
    {
        $subjectEntity = CommunicationTheme::model()->findByAttributes([
            'theme_usage'  => CommunicationTheme::USAGE_OUTBOX,
            'character_id' => $messageToReply->sender_id,
            'text'         => $messageToReply->subject_obj->text,
            'mail_prefix'  => $messageToReply->subject_obj->getPrefixForReply()
        ]); // lowercase is important for search!

        return $subjectEntity;
    }

    /**
     * @param Simulation $simulation
     * @param MailBox $messageToForward
     *
     * @return mixed array
     */
    public static function getForwardMessageData($messageToForward)
    {
        if (NULL === $messageToForward) {
            return array(
                'result' => 0
            );
        }

        $characterThemeId = null;
        // it is extremly important to find proper  Fwd: in database

        $forwardSubject = CommunicationTheme::model()->findByAttributes([
            'mail_prefix'  => $messageToForward->subject_obj->getPrefixForForward(),
            'text'         => $messageToForward->subject_obj->text,
            'character_id' => null,
            'theme_usage'  => CommunicationTheme::USAGE_OUTBOX,
        ]);
        
        if (NULL === $forwardSubject) {
            return array(
                'result' => 0,
                'error'  => 'Can`t find subject for forward email.'
            );
        }

        $result = [
            'parentSubjectId'   => $messageToForward->subject_obj->id,
        ];

        // загрузить фразы по старой теме
        if (null !== $forwardSubject && 0 < $forwardSubject->id) {
            if ($forwardSubject->constructor_number === 'TXT') {
                $result['text'] = $forwardSubject->getMailTemplate()->message;
            } else {
                $result['phrases']['data'] = MailBoxService::getMailPhrases($messageToForward->simulation, $forwardSubject->id);
                $result['subjectId'] = $forwardSubject->id;
            }
        }

        if (!isset($result['phrases']) && !isset($result['text'])) {
            $result['phrases']['data'] = MailBoxService::getMailPhrases($messageToForward->simulation);
        } // берем дефолтные
        $result['phrases']['addData'] = MailBoxService::getSigns($messageToForward->simulation);


        $result['result']    = 1;
        $result['subject']   = (null === $forwardSubject) ? null : $forwardSubject->getFormattedTheme();
        $result['subjectId'] = (null === $forwardSubject) ? null : $forwardSubject->id;

        $result['phrases']['previouseMessage'] = $messageToForward->message;

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
            EventsManager::startEvent($simulation, $mailFlag->mail_code, false, false, 0);
        }
    }
}