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
     * @param array $ids
     *
     * @return array
     */
    public static function getCharacters($ids = array())
    {
        $resultCharacters = array();

        $query = Character::model();
        if (0 < count($ids)) {
            $query->byIds($ids);
        }
        $charactersCollection = $query->findAll();

        foreach ($charactersCollection as $character) {
            $resultCharacters[$character->id] = $character->fio . ' <' . $character->email . '>';
        }

        return $resultCharacters;
    }

    protected static function processSubject($subject)
    {
        // новая сортировка
        $subject = mb_strtolower($subject, 'UTF8');
        $subject = preg_replace("/^(re:)*/u", '', $subject);
        $subject = preg_replace("/^(fwd:)*/u", '', $subject);
        return $subject;
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

        $order = (isset($params['order'])) ? $params['order'] : false;
        if ($order == -1) {
            $order = false;
        }

        $orderField = false;
        if ($order == 'sender') $orderField = 'sender_id';
        //if ($order == 'time') $orderField = 'receiving_date'; TODO:Могут быть проблемы из-за того что уже нет столбца receiving_date в mail_template

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
        $characters = self::getCharacters();
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
            if ($folderId == 2 || $folderId == 3) $readed = 1;

            // загрузим ка получателей {
            $receivers = MailRecipient::model()->byMailId($message->id)->findAll();
            $receiversCollection = [];

            if (count($receivers) == 0) {
                $receiversCollection[] = $characters[$receiverId];
            }

            foreach ($receivers as $receiver) {
                $receiversCollection[] = $characters[$receiver->receiver_id];
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

            $item['subjectSort'] = self::processSubject($subject);


            $list[(int)$message->id] = $item;
        }


        if ($orderType == 'ASC') $ordeFlag = SORT_ASC;
        else $ordeFlag = SORT_DESC;

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
            $subjects[$key] = $row['subjectSort'];
            $senders[$key] = $row['sender'];
            $receivers[$key] = $row['receiver'];

        }

        if ($order == 'subject') {
            array_multisort($subjects, $ordeFlag, $list);
        }

        if ($order == 'sender') {
            array_multisort($senders, $ordeFlag, $list);
        }

        if ($order == 'receiver') {
            array_multisort($receivers, $ordeFlag, $list);
        }

        return $list;
    }

    /**
     * Загрузка одиночного сообщения
     * @param int $id
     * @return array
     */
    public static function getMessage($id)
    {
        $model = MailBox::model()->byId($id)->find();
        if (!$model) return array();

        // mark Readed
        $model->readed = 1;
        $model->save();
        $themes = CommunicationTheme::model()->byId($model->subject_id)->find();
        $subject = $themes->text;

        $message = array(
            'id' => $model->id,
            'subject' => $subject,
            'message' => $model->message,
            'sentAt' => GameTime::getDateTime($model->sent_at),
            'sender' => $model->sender_id,
            'receiver' => $model->receiver_id
        );
        $message_id = $model->message_id;

        // array($model->sender_id, $model->receiver_id)
        // Получим всех персонажей
        $characters = self::getCharacters();

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
            $message['message'] = self::buildMessage($model->id);
        }

        $message['attachments'] = MailAttachmentsService::get($model->id);

        if (!empty($message_id)) {
            $reply = MailBox::model()->byId($message_id)->find();
            $message['reply'] = $reply->message;
        }
        return $message;
    }

    public static function saveCopies($receivers, $mailId)
    {
        $receivers = explode(',', $receivers);
        if ($receivers[count($receivers) - 1] == '') {
            unset($receivers[count($receivers) - 1]);
        }

        foreach ($receivers as $receiverId) {
            $model = new MailCopy();
            $model->mail_id = $mailId;
            $model->receiver_id = $receiverId;
            $model->insert();
        }
    }

    public static function getMailPhrases($id = false)
    {
        $phrases = array();

        if (false !== $id && NULL !== $id) {
            // получить код набора фраз
            $communicationTheme = CommunicationTheme::model()->byId($id)->find();
            // Если у нас прописан какой-то конструктор
            if ($communicationTheme) {
                $constructorNumber = $communicationTheme->constructor_number;
                // получить фразы по коду
                $phrases = MailPhrase::model()->byCode($constructorNumber)->findAll();

                $list = array();
                foreach ($phrases as $model) {
                    $list[$model->id] = $model->name;
                }
                return $list;
            }
        }

        // конструтор не прописан - вернем дефолтовый
        if (count($phrases) == 0) {
            $phrases = MailPhrase::model()->byCode('B1')->findAll();           
        };
        $list = array();
        foreach ($phrases as $model) {
            $list[$model->id] = $model->name;
        }

        return $list;
    }

    public static function getSigns()
    {
        $phrases = MailPhrase::model()->byCode('SYS')->findAll();

        $list = array();
        foreach ($phrases as $model) {
            $list[$model->id] = $model->name;
        }

        return $list;
    }

    public function getMailPhrasesByCharacterAndTheme($characterId, $themeId)
    {
        $model = CommunicationTheme::model()->byCharacter($characterId)->byTheme($themeId)->find();
        if (!$model) return false;
        return $this->getMailPhrases($model->id);
    }

    /**
     * Установка дефолтовых значений при старте симуляции
     * @param type $simId
     */
    public static function initDefaultSettings($simId)
    {
        $model = new MailSettings();
        $model->sim_id = $simId;
        $model->insert();

        return true;
    }

    /**
     * Сборка сообщения
     * @param int $mailId
     * @return string
     */
    public static function buildMessage($mailId)
    {
        $mail = MailBox::model()->findByPk($mailId);
        $characterTheme = $mail->subject_obj;
        if ($characterTheme && $characterTheme->constructor_number == 'TXT') {
            // MailTemplate indexed by MySQL id insteda of out code, so $characterTheme->letter relation doesn`t work
            $mailTemplate = MailTemplate::model()->byCode($characterTheme->letter_number)->find();
            return $mailTemplate->message;
        }
        ;
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
     * Сохранить получателей сообщения
     * @param array $receivers
     */
    public static function saveReceivers($receivers, $mailId)
    {
        if (count($receivers) == 0) return false;

        if ($receivers[count($receivers) - 1] == '') unset($receivers[count($receivers) - 1]);

        foreach ($receivers as $receiverId) {
            $model = new MailRecipient();
            $model->mail_id = $mailId;
            $model->receiver_id = $receiverId;
            $model->insert();
        }
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
                'text = :text AND character_id = :character_id AND mail_prefix = :mail_prefix AND (theme_usage NOT LIKE :outbox_old OR theme_usage is null)', [
                'mail_prefix'  => $parentSubject->getPrefixForForward(), 
                'text'         => $parentSubject->text,
                'character_id' => $receivers[0],
                'outbox_old'   => 'mail_outbox_old'
            ]);
            if (NULL !== $model) {
                $models[] = $model;
            }
        } else {
            // this is NEW mail
            $models = CommunicationTheme::model()->findAll(
                'character_id = :character_id AND mail_prefix IS NULL AND mail = 1 AND theme_usage NOT LIKE :outbox_old ', [
                'character_id' => $receivers[0],
                'outbox_old'   => 'mail_outbox_old'
            ]);
        }

        foreach ($models as $model) {
            $themes[(int)$model->id] = $model->getFormattedTheme();
        }

        return $themes;
    }

    /**
     * @param integer $id
     */
    public function delete($id)
    {
        $model = MailBox::model()->byId($id)->find();
        if (NULL !== $model) {
            $model->group_id = MailBox::TRASH_FOLDER_ID;
            $model->save();
            return true;
        }
        return false;
    }

    /**
     * Копирование сообщения из шаблонов писем в текущую симуляцию по коду
     * @param Simulation $simulation
     * @param type $code
     */
    public static function copyMessageFromTemplateByCode($simulation, $code)
    {
        // проверим а вдруг у нас уже есть такое сообщение
        $mailModel = MailBox::model()->byCode($code)->bySimulation($simulation->id)->find();
        if ($mailModel) return $mailModel; // сообщение уже есть у нас


        // проверим есть ли такоо сообщение вообще
        $mail = MailTemplate::model()->byCode($code)->find();
        if (!$mail) return false; // нечего копировать

        // копируем само письмо
        $connection = Yii::app()->db;
        $sql = "insert into mail_box
            (sim_id, template_id, group_id, sender_id, sent_at, receiver_id, message, subject_id, code, type)
            select :simId, id, group_id, sender_id, sent_at, receiver_id, message, subject_id, code, type
            from mail_template
            where mail_template.code = '{$code}'";

        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simulation->id, PDO::PARAM_INT);
        $command->execute();

        $mailModel = MailBox::model()->byCode($code)->bySimulation($simulation->id)->find();
        if (!$mailModel) return false; // что-то пошло не так - письмо не скопировалось в симуляцию

        // move from 5 (not send) to inbox (1)
        if (MailBox::NOT_RECEIVED_EMAILS_GROUP_ID == $mailModel->group_id) {
            $mailModel->group_id = MailBox::INBOX_FOLDER_ID;
            $mailModel->save();
            $mailModel->refresh();
        }

        return self::_copyMessageSructure($mailModel, $simulation->id);
    }
    
    /**
     * This method must be use at simStart only and must be removed/updated after release
     * plain SQL to make code faster
     * 
     * @param Email $mail
     * @param integer $simId
     * @param type $documents
     * @return string
     */
    protected static function _getCopyMessageSructureSql($mail, $simId, $documents) 
    {
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
        if(isset($documents[$mail->template_id])) {
            $sql .= "insert into mail_attachments (mail_id, file_id)".
                " values ({$mail->id}, {$documents[$mail->template_id]});";
        }
            
        return $sql;
    }

    protected static function _copyMessageSructure($mail, $simId)
    {
        $connection = Yii::app()->db;
        $id = $mail->id;
        $templateId = $mail->template_id;
        // выберем копии из шаблона
        $sql = "insert into mail_copies (mail_id, receiver_id) select :mailId, receiver_id from mail_copies_template where mail_id=:templateId";
        $command = $connection->createCommand($sql);
        $command->bindParam(":mailId", $id, PDO::PARAM_INT);
        $command->bindParam(":templateId", $templateId, PDO::PARAM_INT);
        $command->execute();

        // учтем множественных получателей
        $sql = "insert into mail_receivers (mail_id, receiver_id) select :mailId, receiver_id from mail_receivers_template where mail_id=:templateId";
        $command = $connection->createCommand($sql);
        $command->bindParam(":mailId", $id, PDO::PARAM_INT);
        $command->bindParam(":templateId", $templateId, PDO::PARAM_INT);
        $command->execute();

        // учесть вложение
        $sql = "select file_id from mail_attachments_template where mail_id = :mailId";

        $command = $connection->createCommand($sql);
        $command->bindParam(":mailId", $templateId, PDO::PARAM_INT);
        $row = $command->queryRow();

        if (isset($row['file_id'])) {
            // определить file_id в симуляции
            $file = MyDocument::model()->bySimulation($simId)->byTemplateId((int)$row['file_id'])->find();
            if (!$file) {
                // документа еще нет в симуляции
                $fileId = MyDocumentsService::copyToSimulation($simId, $row['file_id']);
            } else {
                $fileId = $file->id;
            }

            if ($fileId > 0) {
                $attachment = new MailAttachment();
                $attachment->mail_id = $id;
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

        return $mail;
    }

    /**
     * Копирование шаблонов писем в рамках заданной симуляции
     * @param int $simId
     */
    public static function initMailBoxEmails($simId)
    {
        $profiler = new SimpleProfiler(false);
        $profiler->startTimer();    
        $profiler->render('r1: ');
        
        $connection = Yii::app()->db;
        $sql = "insert into mail_box
            (sim_id, template_id, group_id, sender_id, receiver_id, message, subject_id, code, sent_at, type)
            select :simId, id, group_id, sender_id, receiver_id, message, subject_id, code, sent_at, type
            from mail_template  where group_id IN (1,3) ";
        $profiler->render('r2: ');

        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $inboxId = MailBox::INBOX_FOLDER_ID;
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

        $sql = '';

        foreach ($mailCollection as $mail) {
            // plain SQL to make code faster
            $sql .= self::_getCopyMessageSructureSql($mail, $simId, $docIds);

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

    public static function getUnreadInfo($mailId, $simId)
    {
        // получить колличество непрочитанных сообщений
        $model = MailBox::model()->byId($mailId)->find();
        $folderId = (int)$model->group_id;


        // добавляем информацию о колличестве непрочитанных сообщений в подпапках
        $sql = "SELECT COUNT( * ) AS count
                FROM  `mail_box`
                WHERE sim_id = :simId AND readed = 0 and group_id = :groupId";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $command->bindParam(":groupId", $folderId, PDO::PARAM_INT);
        $row = $command->queryRow();

        $result = array();
        $result['folderId'] = $folderId;
        $result['unreaded'] = $row['count'];

        return $result;
    }

    public static function getFoldersUnreadCount($simId)
    {
        // добавляем информацию о колличестве непрочитанных сообщений в подпапках
        $sql = "SELECT COUNT( * ) AS count, group_id
                FROM  `mail_box`
                WHERE sim_id = :simId AND readed = 0 AND group_id != 5
                GROUP BY group_id";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $data = $command->queryAll();

        $folders = array();
        foreach ($data as $row) {
            $folders[$row['group_id']] = $row['count'];
        }

        $folders[2] = 0;
        $folders[3] = 0;

        return $folders;
    }

    /**
     * Определить идентификатор шаблона письма, на основании которого создано письмо
     * @param int $mailId
     */
    public static function getTemplateId($mailId)
    {
        $model = MailBox::model()->byId($mailId)->find();
        if (!$model) throw new Exception("cant find mail by id = $mailId");
        return $model->template_id;
    }

    public static function getTasks($templateId)
    {
        $collection = MailTask::model()->byMailId($templateId)->findAll();

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
     * Определить тему по ее идентификатору
     * @param int $subjectId
     * @return string
     */
    public static function getSubjectById($subjectId)
    {
        $subjectModel = CommunicationTheme::model()->byId($subjectId)->find();
        if ($subjectModel) {
            return $subjectModel->text;
        }

        return false;
    }

    /**
     * Создает тему
     *
     * @param string $subject
     * @return int
     */
    public static function createSubject($subject)
    {
        $subjectModel = new CommunicationTheme();
        $subjectModel->text = $subject;
        $subjectModel->insert();
        return $subjectModel->id;
    }

    public static function getSubjectByText($subjectText, $mailPrefix = null)
    {
        $model = CommunicationTheme::model()->findByAttributes(['text' => $subjectText, 'mail_prefix' => $mailPrefix]);

        return (null === $model) ? null : $model;
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
     *
     *
     * @param integer $mailId, It must by ID of MS (sended from user) email
     * @param integer $simId
     * @return mixed
     */
    public static function updateMsCoincidence($mailId, $simId)
    {
        $emailConsidenceAnalizator = new EmailCoincidenceAnalyzer();
        $emailConsidenceAnalizator->setUserEmail($mailId);
        $result = $emailConsidenceAnalizator->checkCoincidence();

        // update check MS email concidence
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
            FlagsService::setFlag($simId, $mail->template->flag_to_switch, 1);
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
    public static function getPhrases($characterThemeId, $forwardLetterCharacterThemesId)
    {
        $data = array();
        $addData = array();
        $message = '';

        // for forwarded letters
        if ((int)$characterThemeId == 0 && (int)$forwardLetterCharacterThemesId != 0) {
            $characterThemeId = $forwardLetterCharacterThemesId;
        }

        if ((int)$characterThemeId == 0) {
            $data = self::getMailPhrases();
            $addData = self::getSigns();
        }

        $characterTheme = CommunicationTheme::model()->findByPk($characterThemeId);

        if (NULL !== $characterTheme &&
            'TXT' === $characterTheme->constructor_number
        ) {
            // MailTemplate indexed by MySQL id insteda of out code, so $characterTheme->letter relation doesn`t work
            $mailTemplate = MailTemplate::model()->byCode($characterTheme->letter_number)->find();
            $message = $mailTemplate->message;
        } else {
            $data = self::getMailPhrases($characterThemeId);
            $addData = self::getSigns();
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

        assert($sendMailOptions->messageId !== null); // wtf ?

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

        $sendEmail->letter_type = $sendMailOptions->getLetterType();
        if ($letterType != 'new') {
            $sendEmail->message_id = $sendMailOptions->messageId;
        }

        $sendEmail->sim_id = $sendMailOptions->simulation->id;

        $sendEmail->insert();

        // сохранение копий
        if (null != $sendMailOptions->copies) {
            self::saveCopies($sendMailOptions->copies, $sendEmail->id);
        }

        self::saveReceivers($receivers, $sendEmail->id);

        // учтем аттачмена
        if (null !== $sendMailOptions->fileId) {
            MailAttachmentsService::refresh($sendEmail->id, $sendMailOptions->fileId);
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
        $sendMailOptions->groupId   = MailBox::DRAFTS_FOLDER_ID;
        $sendMailOptions->senderId  = Character::HERO_ID;

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
            $email->group_id == MailBox::DRAFTS_FOLDER_ID ||
            $email->group_id == MailBox::OUTBOX_FOLDER_ID ||
            $folderId == MailBox::DRAFTS_FOLDER_ID ||
            $folderId == MailBox::OUTBOX_FOLDER_ID
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
            MailBox::DRAFTS_FOLDER_ID == $messageToReply->group_id ||
            MailBox::OUTBOX_FOLDER_ID == $messageToReply->group_id
        ) {
            return array();
        }
        ;

        // init default responce
        $result = array(
               'message'          => '',
            'data'             => self::getMailPhrases(),
            'previouseMessage' => $messageToReply->message,
            'addData'          => self::getSigns()
        );

        if ($characterThemeModel) {
            $characterThemeId = $characterThemeModel->id;
            $mailTemplate = $characterThemeModel->getMailTemplate();
            if ($characterThemeModel->constructor_number === 'TXT') {
                $result['message'] = (NULL === $mailTemplate) ? '' : $mailTemplate->message;
                $result['data']    = [];
                $result['addData'] = [];
            } else {
                $result['data'] = self::getMailPhrases($characterThemeId);
            }
        }
        // get phrases }

        return $result;
    }
    
    /**
     * @param MailBox $messageToForward
     * @return CommunicationTheme
     */
    public static function getSubectForForwardEmail($messageToForward)
    {
        return CommunicationTheme::model()->find(
            'text = :text AND character_id = :character_id AND mail_prefix = :mail_prefix',[
            'mail_prefix'  => $messageToForward->subject_obj->getPrefixForForward(), 
            'text'         => $messageToForward->subject_obj->text,
            'character_id' => $messageToForward->receiver_id
        ]);
    }

    /**
     * @params CommunicationTheme $messageToReply
     */
    public static function getSubjectForRepryEmail($messageToReply)
    {
        $previousEmalSubjectEntity = CommunicationTheme::model()->findByPk($messageToReply->subject_id);
        
        $mail_prefix = 're';
        
        switch($messageToReply->subject_obj->mail_prefix) {
            case 're': 
                $mail_prefix = 'rere';
                break;
            case 'rere': 
                $mail_prefix = 'rerere';
                break;
            case 'fwd': 
                $mail_prefix = 'refwd';
                break;
            case 'rerere': 
                $mail_prefix = 'rererere';
                break;
        }

        # TODO: refactor this. name is not unique
        $subjectEntity = CommunicationTheme::model()->findByAttributes([
            'text'        => $previousEmalSubjectEntity->text,
            'mail_prefix' => $mail_prefix
        ]); // lowercase is important for search!

        return $subjectEntity;
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
            if (Character::model()->findByAttributes(['code' => Character::HERO_ID])->primaryKey !== $model->receiver_id) {
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
     * @todo: merge with self::getTasks()
     *
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

        return MailBoxService::getTasks($email->template_id);
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
        $email->group_id = MailBox::OUTBOX_FOLDER_ID;
        $email->save();

        // update email folder }

        MailBoxService::updateRelatedEmailForByReplyToAttribute($email);

        MailBoxService::updateMsCoincidence($email->id, $simulation->id);

        return true;
    }

    /**
     * @param Simulation $simulation
     * @param MailBox $messageToForward
     *
     * @return mixed array
     */
    public static function getForwardMessageData($simulation, $messageToForward)
    {
        if (NULL === $messageToForward) {
            return array(
                'result' => 0
            );
        }

        $sender           = $messageToForward->sender_id;
        $characterThemeId = null;
        // it is extremly important to find proper  Fwd: in database

        $forwardSubject = self::getSubectForForwardEmail($messageToForward);
        
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
        $service = new MailBoxService();

        if (0 < $forwardSubject->id) {
                if ($forwardSubject->constructor_number === 'TXT') {
                    $result['text'] = $forwardSubject->getMailTemplate()->message;
                } else {
                    $result['phrases']['data'] = MailBoxService::getMailPhrases($forwardSubject->id);
                    $result['subjectId'] = $forwardSubject->id;
                }
            //}
        }

        if (!isset($result['phrases']) && !isset($result['text'])) {
            $result['phrases']['data'] = MailBoxService::getMailPhrases();
        } // берем дефолтные
        $result['phrases']['addData'] = MailBoxService::getSigns();


        $result['result']    = 1;
        $result['subject']   = $forwardSubject->getFormattedTheme();
        $result['subjectId'] = $forwardSubject->id;

        $result['phrases']['previouseMessage'] = $messageToForward->message;

        return $result;
    }

    /**
     * @param Simulation $simulation
     * @param string $flag, like 'F1', 'F2'
     */
    public static function sendEmailsRelatedToFlag($simulation, $flag) {
        $mailFlags = FlagRunMail::model()->findAllByAttributes([
            'flag_code' => $flag
        ]);

        foreach ($mailFlags as $mailFlag) {
            $em = new EventsManager();
            $em->startEvent($simulation->id, $mailFlag->mail_code, false, false, 0);
        }
    }
}