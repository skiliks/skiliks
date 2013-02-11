<?php

/**
 * Description of MailBoxService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailBoxService
{

    /**
     * Получить список папок и писем в них
     * @param Simulations $simulation
     * @return array
     */
    public static function getFolders($simulation)
    {
        $folders = MailFoldersModel::getFoldersListForJson();

        $inboxMessages = self::getMessages(array(
            'folderId' => MailFoldersModel::INBOX_ID, // inbox
            'receiverId' => Characters::HERO_ID,
            'simId' => $simulation->id
        ));

        $sendedMessages = self::getMessages(array(
            'folderId' => MailFoldersModel::SENDED_ID, // inbox
            'receiverId' => Characters::HERO_ID,
            'simId' => $simulation->id
        ));

        $unreadInfo = MailBoxService::getFoldersUnreadCount($simulation->id);
        foreach ($unreadInfo as $folderId => $count) {
            $folders[$folderId]['unreaded'] = $count;
        }

        return array(
            $folders,
            [
                'inbox' => $inboxMessages,
                'sended' => $sendedMessages
            ]
        );
    }

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

        $query = Characters::model();
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
        $folderId = $params['folderId'];
        $receiverId = $params['receiverId'];
        $simId = $params['simId'];


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

        $model = MailBoxModel::model();
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
            $users[$senderId] = $senderId;
            $users[$receiverId] = $receiverId;
            /** @var $theme CommunicationTheme */
            $theme = CommunicationTheme::model()->byId($message->subject_id)->find();

            $subject = $theme->getFormattedTheme();

            $readed = $message->readed;
            // Для черновиков и исходящих письма всегда прочитаны - fix issue 69
            if ($folderId == 2 || $folderId == 3) $readed = 1;

            // загрузим ка получателей
            $receivers = MailReceiversModel::model()->byMailId($message->id)->findAll();
            $receiversCollection = array();

            if (count($receivers) == 0)
                $receiversCollection[] = $characters[$receiverId];

            foreach ($receivers as $receiver) {
                $receiversCollection[] = $characters[$receiver->receiver_id];
            }

            $item = array(
                'id' => $message->id,
                'subject' => $subject,
                'sentAt' => GameTime::getDateTime($message->sent_at),
                'sender' => $characters[$senderId],
                'receiver' => implode(',', $receiversCollection),
                'readed' => $readed,
                'attachments' => 0
            );

            $item['subjectSort'] = self::processSubject($subject);


            $list[(int)$message->id] = $item;
        }


        if ($orderType == 'ASC') $ordeFlag = SORT_ASC;
        else $ordeFlag = SORT_DESC;

        // Добавим информацию о вложениях
        if (count($mailIds) > 0) {
            $attachments = MailAttachmentsModel::model()->byMailIds($mailIds)->findAll();
            foreach ($attachments as $attachment) {
                if (isset($list[$attachment->mail_id])) {
                    $list[$attachment->mail_id]['attachments'] = 1;
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
        $model = MailBoxModel::model()->byId($id)->find();
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
        $receivers = MailReceiversModel::model()->byMailId($id)->findAll();
        $receiversCollection = array();

        if (count($receivers) == 0)
            $receiversCollection[] = $characters[$message['receiver']];

        foreach ($receivers as $receiver) {
            $receiversCollection[] = $characters[$receiver->receiver_id];
        }
        $message['receiver'] = implode(',', $receiversCollection);

        // загрузим копии
        $copies = MailCopiesModel::model()->byMailId($id)->findAll();
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
            $reply = MailBoxModel::model()->byId($message_id)->find();
            $message['reply'] = $reply->message;
        }
        return $message;
    }

    /**
     * Sends message in the internal mail client
     *
     * @param array $params dictionary with elements letterType, subject
     * @return \MailBoxModel
     */
    public static function sendMessage($params)
    {

        $subject_id = $params['subject_id'];
        $message_id = $params['message_id'];
        assert($message_id !== null);

        $letterType = false;
        if (isset($params['letterType'])) $letterType = $params['letterType'];

        if (false === is_array($params['receivers'])) {
            $receivers = explode(',', $params['receivers']);
        } else {
            $receivers = $params['receivers'];
        }
        $receiverId = (int)$receivers[0];

        $message = new MailBoxModel();
        $message->group_id = $params['group'];
        $message->sender_id = $params['sender'];
        $message->subject_id = $subject_id;
        $message->receiver_id = $receiverId;
        $message->sent_at = GameTime::setTimeToday($params['time']); //TODO: Время, проверить
        $message->readed = 0;
        $message->letter_type = $params['letterType'];
        if ($letterType != 'new') {
            $message->message_id = $message_id;
        }
        $message->sim_id = $params['simId'];

        $message->insert();

        $mailId = $message->id;
        //Создаем лог в ручную

        // сохранение копий
        if (isset($params['copies'])) {
            if ($params['copies'] != '')
                self::saveCopies($params['copies'], $mailId);
        }

        if (isset($params['receivers'])) {
            Logger::write(var_export($params['receivers'], true));
            self::saveReceivers($receivers, $mailId);
        }

        // учтем аттачмена
        if (isset($params['fileId'])) {
            MailAttachmentsService::refresh($mailId, $params['fileId']);
        }

        // Сохранение фраз
        if (isset($params['phrases'])) {
            $phrases = explode(',', $params['phrases']);

            foreach ($phrases as $phraseId) {
                if (null !== $phraseId && 0 != $phraseId && '' != $phraseId) {
                    $msg_model = new MailMessagesModel();
                    $msg_model->mail_id = $mailId;
                    $msg_model->phrase_id = $phraseId;
                    $msg_model->insert();
                }
            }
        }

        return $message;
    }


    public static function saveCopies($receivers, $mailId)
    {
        $receivers = explode(',', $receivers);
        if ($receivers[count($receivers) - 1] == '') unset($receivers[count($receivers) - 1]);

        foreach ($receivers as $receiverId) {
            $model = new MailCopiesModel();
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
                $phrases = MailPhrasesModel::model()->byCode($constructorNumber)->findAll();

                $list = array();
                foreach ($phrases as $model) {
                    $list[$model->id] = $model->name;
                }
                return $list;
            }
        }

        // конструтор не прописан - вернем дефолтовый
        if (count($phrases) == 0) {
            $phrases = MailPhrasesModel::model()->byCode('B1')->findAll();           
        };
        $list = array();
        foreach ($phrases as $model) {
            $list[$model->id] = $model->name;
        }

        return $list;
    }

    public static function getSigns()
    {
        $phrases = MailPhrasesModel::model()->byCode('SYS')->findAll();

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
        $model = new MailSettingsModel();
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
        $mail = MailBoxModel::model()->findByPk($mailId);
        $characterTheme = $mail->subject_obj;
        if ($characterTheme && $characterTheme->constructor_number == 'TXT') {
            // MailTemplate indexed by MySQL id insteda of out code, so $characterTheme->letter relation doesn`t work
            $mailTemplate = MailTemplateModel::model()->byCode($characterTheme->letter_number)->find();
            return $mailTemplate->message;
        }
        ;
        $models = MailMessagesModel::model()->byMail($mailId)->findAll();

        $phrases = array();
        foreach ($models as $model) {
            $phrases[] = $model->phrase_id;
        }

        // получение набора фраз
        $phrasesCollection = MailPhrasesModel::model()->byIds($phrases)->findAll();

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
            $model = new MailReceiversModel();
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
            $models[] = CommunicationTheme::model()->find(
                'text = :text AND character_id = :character_id AND mail_prefix = :mail_prefix',[
                'mail_prefix'  => $parentSubject->getPrefixForForward(), 
                'text'         => $parentSubject->text,
                'character_id' => $receivers[0]
            ]);
        } else {
            // this is NEW mail
            $models = CommunicationTheme::model()->findAllByAttributes([
                'character_id' => $receivers[0],
                'mail_prefix' => null,
                'mail' => 1
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
        $model = MailBoxModel::model()->byId($id)->find();
        if (NULL !== $model) {
            $model->group_id = MailBoxModel::TRASH_FOLDER_ID;
            $model->save();
            return true;
        }
        return false;
    }

    /**
     * Копирование сообщения из шаблонов писем в текущую симуляцию по коду
     * @param type $simId
     * @param type $code
     */
    public static function copyMessageFromTemplateByCode($simId, $code)
    {
        // проверим а вдруг у нас уже есть такое сообщение
        $mailModel = MailBoxModel::model()->byCode($code)->bySimulation($simId)->find();
        if ($mailModel) return $mailModel; // сообщение уже есть у нас


        // проверим есть ли такоо сообщение вообще
        $mail = MailTemplateModel::model()->byCode($code)->find();
        if (!$mail) return false; // нечего копировать

        // копируем само письмо
        $connection = Yii::app()->db;
        $sql = "insert into mail_box
            (sim_id, template_id, group_id, sender_id, sent_at, receiver_id, message, subject_id, code, type)
            select :simId, id, group_id, sender_id, sent_at, receiver_id, message, subject_id, code, type
            from mail_template
            where mail_template.code = '{$code}'";

        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $command->execute();

        $mailModel = MailBoxModel::model()->byCode($code)->bySimulation($simId)->find();
        if (!$mailModel) return false; // что-то пошло не так - письмо не скопировалось в симуляцию

        return self::_copyMessageSructure($mailModel, $simId);
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
            $file = MyDocumentsModel::model()->bySimulation($simId)->byTemplateId((int)$row['file_id'])->find();
            if (!$file) {
                // документа еще нет в симуляции
                $fileId = MyDocumentsService::copyToSimulation($simId, $row['file_id']);
            } else {
                $fileId = $file->id;
            }

            if ($fileId > 0) {
                $attachment = new MailAttachmentsModel();
                $attachment->mail_id = $id;
                $attachment->file_id = $fileId;
                $attachment->insert();

                // проверим тип документа
                $fileTemplate = MyDocumentsTemplateModel::model()->byId($row['file_id'])->find();
                if ($fileTemplate->type != 'start') {
                    $file = MyDocumentsModel::model()->byId($fileId)->find();
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
            from mail_template";
        $profiler->render('r2: ');
        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $command->execute();
        $profiler->render('r3: ');
        // теперь скопируем информацию о копиях писем
        $mailCollection = MailBoxModel::model()->bySimulation($simId)->findAll();
        $profiler->render('r4: ');
        
        // prepare all doc templates
        $documentTemplates = [];
        foreach (MyDocumentsTemplateModel::model()->findAll() as $documentTemplate) {
            $documentTemplates[$documentTemplate->id] = $documentTemplate;
        }
        
        // prepare all docs
        $myDocs = [];
        foreach (MyDocumentsModel::model()->findAllByAttributes(['sim_id' => $simId]) as $myDocument) {
            $myDocs[$myDocument->template_id] = $myDocument;
        }
        
        // init MyDocs for docTemplate in current simumation, if proper MyDoc isn`t exist
        $docIds = [];
        foreach (MailAttachmentsTemplateModel::model()->findAll() as $mailAttachment) {
            if (false === isset($myDocs[$mailAttachment->file_id])) {
                $doc = new MyDocumentsModel();
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
        }
        $profiler->render('r5: '); // 0.92
        
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->execute();
        
        $profiler->render('r6: '); // 4.95
    }

    /**
     * @param int $id
     *
     * @return boolean
     */
    public static function markReaded($id)
    {
        $model = MailBoxModel::model()->byId($id)->find();
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
        $model = MailBoxModel::model()->byId($id)->find();
        if (NULL === $model) {
            return false;
        }

        $model->plan = 1;
        $model->save();
    }

    public static function getUnreadInfo($mailId, $simId)
    {
        // получить колличество непрочитанных сообщений
        $model = MailBoxModel::model()->byId($mailId)->find();
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
        $model = MailBoxModel::model()->byId($mailId)->find();
        if (!$model) throw new Exception("cant find mail by id = $mailId");
        return $model->template_id;
    }

    public static function getTasks($templateId)
    {
        $collection = MailTasksModel::model()->byMailId($templateId)->findAll();

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
     * @param MailBox $sendedEmail
     */
    public static function updateRelatedEmailForByReplyToAttribute($sendedEmail)
    {
        if ($sendedEmail->letter_type == 'reply' OR $sendedEmail->letter_type == 'replyAll') {
            if (!empty($sendedEmail->message_id)) {
                $replyToEmail = MailBoxModel::model()
                    ->byId($sendedEmail->message_id)
                    ->find();
                $replyToEmail->markReplied();
                $replyToEmail->update();
            } else {
                Yii::log(sprintf(
                    "Ошибка, не указан messageId для ответить или ответить всем. Отправленное письмо ID %s.",
                    $sendedEmail->id
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
                'simId' => $simId
            ]
        );
        /** @var $mail MailBoxModel */
        $mail = MailBoxModel::model()->findByPk($mailId);
        $mail->code = $result['result_code'];
        $mail->template_id = $result['result_template_id'];
        $mail->save();
        foreach ($log_mails as $log_mail) {
            $log_mail->full_coincidence = $result['full'];
            $log_mail->part1_coincidence = $result['part1'];
            $log_mail->part2_coincidence = $result['part2'];
            $log_mail->is_coincidence = $result['has_concidence'];
            $log_mail->save();
        }
        $simulationEmail = MailBoxModel::model()->findByPk($mailId);
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
            $mailTemplate = MailTemplateModel::model()->byCode($characterTheme->letter_number)->find();
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
     * @return MailBoxModel|null
     */
    public static function sendMessagePro($sendMailOptions)
    {
        if ($sendMailOptions->isReply() && $sendMailOptions->isValidMessageId()) {
            //Изменяем запись в бд: SK - 708
            $message = MailBoxModel::model()->byId($sendMailOptions->messageId)->find();
            $message->reply = true; //1 - значит что на сообщение отправлен ответ
            $message->update();
        }

        $message = MailBoxService::sendMessage(array(
            'message_id' => $sendMailOptions->messageId,
            'group' => MailBoxModel::OUTBOX_FOLDER_ID,
            'sender' => Characters::model()->findByAttributes(['code' => Characters::HERO_ID])->primaryKey,
            'receivers' => $sendMailOptions->getRecipientsArray(),
            'copies' => $sendMailOptions->copies,
            'subject_id' => $sendMailOptions->subject_id,
            'phrases' => $sendMailOptions->phrases,
            'simId' => $sendMailOptions->simulation->id,
            'letterType' => $sendMailOptions->getLetterType(),
            'fileId' => $sendMailOptions->fileId,
            'time' => $sendMailOptions->time
        ));

        MailBoxService::updateRelatedEmailForByReplyToAttribute($message);

        return $message;
    }

    /**
     * @param SendMailOptions $sendMailOptions
     * @return \MailBoxModel
     */
    public static function saveDraft($sendMailOptions)
    {
        $message = self::sendMessage(array(
            'message_id' => $sendMailOptions->messageId,
            'group' => MailBoxModel::DRAFTS_FOLDER_ID, // черновики писать может только главгый герой
            'sender' => Characters::model()->findByAttributes(['code' => Characters::HERO_ID])->primaryKey,
            'receivers' => $sendMailOptions->getRecipientsArray(),
            'copies' => $sendMailOptions->copies,
            'subject_id' => $sendMailOptions->subject_id,
            'phrases' => $sendMailOptions->phrases,
            'simId' => $sendMailOptions->simulation->id,
            'time' => $sendMailOptions->time,
            'fileId' => $sendMailOptions->fileId,
            'letterType' => $sendMailOptions->getLetterType(),
        ));

        return $message;
    }

    /*
     * @param MailBoxModel $email
     * @param int $folderId
     *
     * @return boolean
     */
    public static function moveToFolder($email, $folderId)
    {
        if (NULL === $email ||
            NULL === $folderId ||
            $email->group_id == MailBoxModel::DRAFTS_FOLDER_ID ||
            $email->group_id == MailBoxModel::OUTBOX_FOLDER_ID ||
            $folderId == MailBoxModel::DRAFTS_FOLDER_ID ||
            $folderId == MailBoxModel::OUTBOX_FOLDER_ID
        ) {
            return false;
        }


        $email->group_id = (int)$folderId;
        $email->save();

        return true;
    }

    /**
     * @param Simulations $simulation
     * @param MailBoxModel $messageToReply
     * @param CommunicationTheme $characterThemeModel
     * @return type
     */
    public static function getPhrasesDataForReply($messageToReply, $characterThemeModel)
    {
        // validation
        if (NULL === $messageToReply ||
            MailBoxModel::DRAFTS_FOLDER_ID == $messageToReply->group_id ||
            MailBoxModel::OUTBOX_FOLDER_ID == $messageToReply->group_id
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
     * @param MailBoxModel $messageToReply
     * @return mixed array
     */
    public static function getCopiesArrayForReplyAll($messageToReply)
    {
        $copiesIds = array();
        $copies = array();

        $collection = MailReceiversModel::model()->byMailId($messageToReply->id)->findAll();

        foreach ($collection as $model) {
            // exclude our hero from copies
            if (Characters::model()->findByAttributes(['code' => Characters::HERO_ID])->primaryKey !== $model->receiver_id) {
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
     * @param MailBoxModel $email
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
        $task->simulation = $simulation->id;
        $task->title = $mailTask->name;
        $task->duration = $mailTask->duration;
        $task->category = $mailTask->category;
        TodoService::createTask($task);

        TodoService::add($simulation->id, $task->id);

        $email->plan = 1;
        $email->save();

        return $task;
    }

    /**
     * @param Simulations $simulation
     * @param MailBox $email
     *
     * @return boolean
     */
    public static function sendDraft($simulation, $email)
    {
        assert($email);

        // update email folder {
        $email->group_id = MailBoxModel::OUTBOX_FOLDER_ID;
        $email->save();

        // update email folder }

        MailBoxService::updateRelatedEmailForByReplyToAttribute($email);

        MailBoxService::updateMsCoincidence($email->id, $simulation->id);

        return true;
    }

    /**
     * @param Simulations $simulation
     * @param MailBoxModel $messageToForward
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
}