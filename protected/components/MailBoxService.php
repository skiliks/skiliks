<?php



/**
 * Description of MailBoxService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailBoxService {
    
    /**
     * Получить список папок и писем в них
     * @return type 
     */
    public static function getFolders($simulation) 
    {
        $folders = MailFoldersModel::getFoldersListForJson();
        
        $messages = self::getMessages(array(
            'folderId'   => $folders[MailFoldersModel::INBOX_ID]['id'], // inbox
            'receiverId' => Characters::HERO_ID,
            'simId'      => $simulation->id
        ));
       
        $unreadInfo = MailBoxService::getFoldersUnreadCount($simulation->id);
        foreach ($unreadInfo as $folderId => $count) {
            $folders[$folderId]['unreaded'] = $count;
        }
        
        return array(
            $folders,
            $messages
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
        
        foreach($charactersCollection as $character) {
            $resultCharacters[$character->id] = $character->fio.' <'.$character->email.'>';
        }
        
        return $resultCharacters;
    }
    
    protected static function processSubject($subject) {
        // новая сортировка
        $subject = mb_strtolower ($subject, 'UTF8');
        $subject = preg_replace("/^(re:)*/u", '', $subject);
        $subject = preg_replace("/^(fwd:)*/u", '', $subject);
        return $subject;
        
        if (preg_match_all("/^(re:)*/u", $subject, $matches)) {
            $re = $matches[0][0];
            $re = explode(':', $re);
            $count = count($re) - 1;
            
            // уберем решки впереди
            $subject = preg_replace("/^(re:)*/u", '', $subject);
            return $subject.$count;
        }
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
        $receiverId = $params['receiverId'];
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
        
        $model = MailBoxModel::model();
        $model->bySimulation($params['simId']);
        
        $model->byFolder($folderId);
        if ($orderField) $model->orderBy($orderField, $orderType);
        $messages = $model->findAll();
       
        $users = array();
        $list = array();
        $mailIds = array();
        foreach($messages as $message) {
            $mailIds[] = (int)$message->id;
            $senderId = (int)$message->sender_id;
            $receiverId = (int)$message->receiver_id;
            $users[$senderId] = $senderId;
            $users[$receiverId] = $receiverId;
            $theme = MailCharacterThemesModel::model()->byId($message->subject_id)->find();

            $subject = $theme->text;

            $readed = $message->readed;
            // Для черновиков и исходящих письма всегда прочитаны - fix issue 69
            if ($folderId == 2 || $folderId == 3) $readed = 1;
            
            $item = array(
                'id' => $message->id,
                'subject' => $subject,
                'sentAt' => GameTime::getDateTime($message->sent_at),
                'sender' => $senderId,
                'receiver' => $message->receiver_id,
                'readed' => $readed,
                'attachments' => 0
            );

            $item['subjectSort'] = self::processSubject($subject);
 
            
            $list[(int)$message->id] = $item;
        }
        
        // проставляем имена персонажей
        $characters = self::getCharacters($users);
        foreach($list as $index=>$item) {
            $list[$index]['sender'] = $characters[$list[$index]['sender']];
            $list[$index]['receiver'] = $characters[$list[$index]['receiver']];
        }
        
        if ($orderType == 'ASC') $ordeFlag = SORT_ASC;
        else $ordeFlag = SORT_DESC;
        
        // Добавим информацию о вложениях
        if (count($mailIds) > 0) {
            $attachments = MailAttachmentsModel::model()->byMailIds($mailIds)->findAll();
            foreach($attachments as $attachment) {
                if (isset($list[$attachment->mail_id])) {
                    $list[$attachment->mail_id]['attachments'] = 1;
                }
            }
        }
        
        // подготовка для сортировки на уровне php
        $receivers = array();
        foreach ($list as $key => $row) {
           $subjects[$key]  = $row['subjectSort'];
           $senders[$key] = $row['sender'];
           $receivers[$key] = $row['receiver'];

        }
        
        if ($order == 'subject') {
            array_multisort($subjects, $ordeFlag,  $list);
        }
        
        if ($order == 'sender') {
            array_multisort($senders, $ordeFlag,  $list);
        }
        
        if ($order == 'receiver') {
            array_multisort($receivers, $ordeFlag,  $list);
        }

        return $list;
    }
    
    /**
     * Загрузка одиночного сообщения
     * @param type $id 
     */
    public static function getMessage($id) {
        $model = MailBoxModel::model()->byId($id)->find();
        if (!$model) return array();
        
        // mark Readed
        $model->readed = 1;
        $model->save();
        $themes = MailCharacterThemesModel::model()->byId($model->subject_id)->find();
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
        
        foreach($receivers as $receiver) {
            $receiversCollection[] = $characters[$receiver->receiver_id];
        }
        $message['receiver'] = implode(',', $receiversCollection);
        
        // загрузим копии
        $copies = MailCopiesModel::model()->byMailId($id)->findAll();
        $copiesCollection = array();
        foreach($copies as $copy) {
            $copiesCollection[] = $characters[$copy->receiver_id];
        }
        $message['copies'] = implode(',', $copiesCollection);
        
        
        $message['sender'] = $characters[$message['sender']];
        
        // Собираем сообщение
        if ($message['message'] == '') {
            $message['message'] = self::buildMessage($model->id);
        }
        
        $message['attachments'] = MailAttachmentsService::get($model->id);
        
        if(!empty($message_id)){
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
    public static function sendMessage($params) {

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
        if($letterType != 'new'){
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
            if (count($receivers)>1)
                self::saveReceivers($receivers, $mailId);
        }
        
        // учтем аттачмена
        if (isset($params['fileId'])) {
            MailAttachmentsService::refresh($mailId, $params['fileId']);
        }
        
        // Сохранение фраз
        if (isset($params['phrases'])) {
            $phrases = explode(',', $params['phrases']);
            
            foreach($phrases as $phraseId) {
                if (null !== $phraseId && 0 != $phraseId && '' != $phraseId) {
                    $msg_model = new MailMessagesModel();
                    $msg_model->mail_id = $mailId;
                    $msg_model->phrase_id = $phraseId;
                    $msg_model->insert();
                }
            }
        }
        
        $logs = array(array(10,13,0,GameTime::timeToSeconds($params['time']), array('mailId'=>$mailId)));
        LogHelper::setMailLog($params['simId'], $logs);
        
        return $message;
    }
    
    
    
    public static function saveCopies($receivers, $mailId) {
        $receivers = explode(',', $receivers);
        if ($receivers[count($receivers)-1] == '') unset($receivers[count($receivers)-1]);
        
        foreach($receivers as $receiverId) {
            $model = new MailCopiesModel();
            $model->mail_id = $mailId;
            $model->receiver_id = $receiverId;
            $model->insert();
        }
    }
    
    public static function getMailPhrases($id = false) {
        $phrases = array();
        
        if (false !== $id && NULL !== $id) {
            // получить код набора фраз
            $mailCharacterTheme = MailCharacterThemesModel::model()->byId($id)->find();
            // Если у нас прописан какой-то конструктор
            if ($mailCharacterTheme) {
                $constructorNumber = $mailCharacterTheme->constructor_number;
                // получить фразы по коду
                $phrases = MailPhrasesModel::model()->byCode($constructorNumber)->findAll();

                $list = array();
                foreach($phrases as $model) {
                    $list[$model->id] = $model->name;
                }
                return $list;
            }
        }

        // конструтор не прописан - вернем дефолтовый 
        if (count($phrases)==0) $phrases = MailPhrasesModel::model()->byCode('B1')->findAll();
        $list = array();
        foreach($phrases as $model) {
            $list[$model->id] = $model->name;
        }
        
        return $list;
    }
    
    public static function getSigns() {
        $phrases = MailPhrasesModel::model()->byCode('SYS')->findAll();
        
        $list = array();
        foreach($phrases as $model) {
            $list[$model->id] = $model->name;
        }
        
        return $list;
    }
    
    public function getMailPhrasesByCharacterAndTheme($characterId, $themeId) {
        $model = MailCharacterThemesModel::model()->byCharacter($characterId)->byTheme($themeId)->find();
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
    public static function buildMessage($mailId) {
        $mail = MailBoxModel::model()->findByPk($mailId);
        $characterTheme = $mail->getCharacterTheme();
        if ($characterTheme && $characterTheme->constructor_number == 'TXT') {
            // MailTemplate indexed by MySQL id insteda of out code, so $characterTheme->letter relation doesn`t work 
            $mailTemplate = MailTemplateModel::model()->byCode($characterTheme->letter_number)->find();
            return $mailTemplate->message;
        };
        $models = MailMessagesModel::model()->byMail($mailId)->findAll();

        $phrases = array();
        foreach($models as $model) {
            $phrases[] = $model->phrase_id;
        }
        
        // получение набора фраз
        $phrasesCollection = MailPhrasesModel::model()->byIds($phrases)->findAll();
        
        $phrasesDictionary = array();
        foreach($phrasesCollection as $phraseModel) {
            $phrasesDictionary[$phraseModel->id] = $phraseModel->name;
        }
        
        $collection = array();
        foreach($phrases as $index => $phraseId) {
            $collection[] = $phrasesDictionary[$phraseId];
        }
        
        // склейка фраз
        return implode(' ', $collection);
    }
    
    /**
     * Сохранить получателей сообщения
     * @param array $receivers
     */
    public static function saveReceivers($receivers, $mailId) {
        if (count($receivers) == 0) return false;
        
        if ($receivers[count($receivers)-1] == '') unset($receivers[count($receivers)-1]);
        
        foreach($receivers as $receiverId) {
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
    public static function getThemes($receivers) 
    {
        $receivers = explode(',', $receivers);
        if ($receivers[count($receivers)-1] == ',') unset($receivers[count($receivers)-1]);
        if ($receivers[count($receivers)-1] == '') unset($receivers[count($receivers)-1]);
        
        $themes = array();
        if (count($receivers) == 1) {
            // загрузка тем по одному персонажу
            $models = MailCharacterThemesModel::model()
                ->byCharacter($receivers[0])
                ->byMail()
                ->findAll();
            
            foreach($models as $model) {
                $themes[(int)$model->id] = (int)$model->id;
            }
        }
        
        // если у нас более одного получателя
        if (count($receivers) > 1) {
            $models = MailCharacterThemesModel::model()->byMail()->findAll();
            $collection = array();
            foreach($models as $model) {
                $collection[] = array(
                    'id' => (int)$model->id,
                    'theme_id' => (int)$model->id
                );
            }
            
            $processedItems = 0;
            while($processedItems < 10) {
                $index = rand(0, 10);
                
                if (isset($collection[$index]))
                if (!isset($themes[$collection[$index]['id']])) {
                    $themes[$collection[$index]['id']] = $collection[$index]['id'];
                    
                }
                $processedItems++;
            }
        }
        
        if (count($themes) == 0) return array();
        
        // загрузка тем
        $themeCollection = MailCharacterThemesModel::model()->byIds($themes)->findAll();
        $captions = array();
        foreach($themeCollection as $themeModel) {
            $captions[(int)$themeModel->id] = $themeModel->text;
        }
        
        foreach($themes as $id => $themeId) {
            // remove all Fwd: and re:
            if (false === strpos($captions[$themeId], 're:') &&
                false === strpos($captions[$themeId], 'Fwd:')) {
                $themes[$id] = $captions[$themeId];
            } else {
                unset($themes[$id]);
            }
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
    public static function copyMessageFromTemplateByCode($simId, $code) {
        // проверим а вдруг у нас уже есть такое сообщение
        $mailModel = MailBoxModel::model()->byCode($code)->bySimulation($simId)->find();
        if ($mailModel) return $mailModel; // сообщение уже есть у нас
        
        
        // проверим есть ли такоо сообщение вообще
        $mail = MailTemplateModel::model()->byCode($code)->find();
        if (!$mail) return false; // нечего копировать
        
        // копируем само письмо
        $connection = Yii::app()->db;
        $sql = "insert into mail_box 
            (sim_id, template_id, group_id, sender_id, sent_at, receiver_id, subject, message, subject_id, code, type)
            select :simId, id, group_id, sender_id, sent_at, receiver_id, subject, message, subject_id, code, type
            from mail_template
            where mail_template.code = '{$code}'";
        
        $command = $connection->createCommand($sql);     
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $command->execute();
        
        $mailModel = MailBoxModel::model()->byCode($code)->bySimulation($simId)->find();
        if (!$mailModel) return false; // что-то пошло не так - письмо не скопировалось в симуляцию
        
        return self::_copyMessageSructure($mailModel, $simId);
    }
    
    protected static function _copyMessageSructure($mail, $simId) {
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
            }
            else  {
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
    public static function initMailBoxEmails($simId) {
        $connection = Yii::app()->db;
        $sql = "insert into mail_box 
            (sim_id, template_id, group_id, sender_id, receiver_id, message, subject_id, code, sent_at, type)
            select :simId, id, group_id, sender_id, receiver_id, message, subject_id, code, sent_at, type
            from mail_template";
        
        $command = $connection->createCommand($sql);     
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $command->execute();
        
        // теперь скопируем информацию о копиях писем
        $mailCollection = MailBoxModel::model()->bySimulation($simId)->findAll();
        foreach($mailCollection as $mail) {
            self::_copyMessageSructure($mail, $simId);
        }
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
    
    public static function getUnreadInfo($mailId, $simId) {
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
    
    public static function getFoldersUnreadCount($simId) {
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
        foreach($data as $row) { 
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
    public static function getTemplateId($mailId) {
        $model = MailBoxModel::model()->byId($mailId)->find();
        if (!$model) throw new Exception("cant find mail by id = $mailId");
        return $model->template_id; 
    }
    
    public static function getTasks($templateId) {
        $collection = MailTasksModel::model()->byMailId($templateId)->findAll();
        
        $tasks = array();
        foreach($collection as $task) {
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
    public static function getSubjectById($subjectId) {
        $subjectModel = MailCharacterThemesModel::model()->byId($subjectId)->find();
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
    public static function createSubject($subject) {
        $subjectModel = new MailCharacterThemesModel();
        $subjectModel->text = $subject;
        $subjectModel->insert();
        return $subjectModel->id;
    }
    
    public static function getSubjectIdByText($subjectText)
    {
        $model = MailCharacterThemesModel::model()->byText($subjectText)->find();
        
        return (null === $model) ? null : $model->id;
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
     */
    public static function updateMsCoincidernce($mailId, $simId)
    {
        $emailConsidenceAnalizator = new EmailCoincidenceAnalizator();
        $emailConsidenceAnalizator->setUserEmail($mailId);
        $result = $emailConsidenceAnalizator->checkCoinsidence();
        $command = Yii::app()->db->createCommand();
        
        // update check MS email concidence
        $command->update(
            "log_mail" , 
            array(
                'full_coincidence'  => $result['full'],
                'part1_coincidence' => $result['part1'],
                'part2_coincidence' => $result['part2'],
                'is_coincidence'    => $result['has_concidence'],
            ), 
            "`mail_id` = {$mailId} AND `end_time` > '00:00:00' AND `sim_id` = {$simId} ORDER BY `window` DESC, `id` DESC LIMIT 1"
        );

        $command->update(
            'mail_box',
            array(
                'code'        => $result['result_code'],
                'template_id' => $result['result_template_id'],
            ),
            "`id` = {$mailId}"
        );

            
        $simulationEmail = MailBoxModel::model()->findByPk($mailId);
        if (null !== $simulationEmail) {
            $simulationEmail->code                  = $result['result_code'];
            $simulationEmail->template_id           = $result['result_template_id'];
            $simulationEmail->coincidence_type      = $result['result_type'];
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
        $data    = array();
        $addData = array();
        $message = '';
        
        // for forwarded letters        
        if ((int)$characterThemeId == 0 && (int)$forwardLetterCharacterThemesId != 0) {
            $characterThemeId = $forwardLetterCharacterThemesId;
        }

        if ((int)$characterThemeId == 0) {
            $data    = self::getMailPhrases();
            $addData = self::getSigns();
        }

        $characterTheme = MailCharacterThemesModel::model()->findByPk($characterThemeId);

        if (NULL !== $characterTheme && 
            'TXT' === $characterTheme->constructor_number) {
            // MailTemplate indexed by MySQL id insteda of out code, so $characterTheme->letter relation doesn`t work 
            $mailTemplate = MailTemplateModel::model()->byCode($characterTheme->letter_number)->find();
            $message = $mailTemplate->message;
        } else {
            $data    = self::getMailPhrases($characterThemeId);
            $addData = self::getSigns();
        }
        
        return array(
            'data'    => $data,
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
            'group'      => MailBoxModel::OUTBOX_FOLDER_ID,
            'sender'     => Characters::HERO_ID,
            'receivers'  => $sendMailOptions->getRecipientsArray(),
            'copies'     => $sendMailOptions->copies,
            'subject_id' => $sendMailOptions->subject_id,
            'phrases'    => $sendMailOptions->phrases,
            'simId'      => $sendMailOptions->simulation->id,
            'letterType' => $sendMailOptions->getLetterType(),
            'fileId'     => $sendMailOptions->fileId,
            'time'       => $sendMailOptions->time
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
            'group'      => MailBoxModel::DRAFTS_FOLDER_ID, // черновики писать может только главгый герой
            'sender'     => Characters::HERO_ID,
            'receivers'  => $sendMailOptions->getRecipientsArray(),
            'copies'     => $sendMailOptions->copies,
            'subject_id' => $sendMailOptions->subject_id,
            'phrases'    => $sendMailOptions->phrases,
            'simId'      => $sendMailOptions->simulation->id,
            'time' => $sendMailOptions->time,
            'fileId'     => $sendMailOptions->fileId,
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
            $folderId == MailBoxModel::OUTBOX_FOLDER_ID) {
            return false;
        }


        $email->group_id = (int)$folderId;
        $email->save();
        
        return true;
    }
    
    /**
     * @param Simulations $simulation
     * @param MailBoxModel $messageToReply
     * @param MailCharacterThemesModel $characterThemeModel
     * @return type
     */
    public static function getPhrasesDataForReply($messageToReply, $characterThemeModel)
    {
        // validation
        if (NULL === $messageToReply ||
            MailBoxModel::DRAFTS_FOLDER_ID == $messageToReply->group_id ||
            MailBoxModel::OUTBOX_FOLDER_ID == $messageToReply->group_id) {
            return array();
        };
        
        // init default responce
        $result = array(
            'message'          => NULL,
            'data'             => array(),
            'previouseMessage' => $messageToReply->message,
            'addData'          => self::getSigns()
        );

        if ($characterThemeModel) {
            $characterThemeId = $characterThemeModel->id;
            if ($characterThemeModel->constructor_number === 'TXT') {
                $result['message'] = $characterThemeModel->letter->message;
            } else {
                $result['data'] = self::getMailPhrases($characterThemeId);
            }
        }
        // get phrases }
        
        // set defaults if there are no phrases
        if (0 == count($result['data'])) {
            $result['data'] = self::getMailPhrases();
        }
        
        return $result;
    }
    
    /**
     * @params MailCharacterThemesModel $messageToReply
     */
    public static function getSubjectForRepryEmail($messageToReply)
    {
        $previousEmalSubjectEntity = MailCharacterThemesModel::model()->findByPk($messageToReply->subject_id);

        # TODO: refactor this. name is not unique
        $subjectEntity = MailCharacterThemesModel::model()->byText('re: ' . $previousEmalSubjectEntity->text)->find();// lowercase is important for search!

        return $subjectEntity;
    }
    
    /**
     * @param MailBoxModel $messageToReply
     * @return mixed array
     */
    public static function getCopiesArrayForReplyAll($messageToReply)
    {
        $copiesIds = array();
        $copies    = array();

        $collection = MailReceiversModel::model()->byMailId($messageToReply->id)->findAll();

        foreach ($collection as $model) {
            // exclude our hero from copies
            if (Characters::HERO_ID !== (int)$model->receiver_id) {
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
        $task->title      = $mailTask->name;
        $task->duration   = $mailTask->duration;
        $task->category   = $mailTask->category;
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
        if (NULL == $email) {
            return false;
        }
        
        // update email folder {
        $email->group_id = MailBoxModel::OUTBOX_FOLDER_ID;
        $email->save();;
        // update email folder }

        MailBoxService::updateRelatedEmailForByReplyToAttribute($email);

        MailBoxService::updateMsCoincidernce($email->id, $simulation->id);
        
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
        
        $forwardSubjectId  = $messageToForward->subject_id;
        $sender            = $messageToForward->sender_id;
        $receiverId        = $messageToForward->receiver_id;

        $forwardSubjectText = 'Fwd: ' . $messageToForward->subject_obj->text; // 'Fwd: ' with space-symbol, 
        // it is extremly important to find proper  Fwd: in database

        $forwardSubjectId = MailBoxService::getSubjectIdByText($forwardSubjectText);

        $result = array();

        // загрузить фразы по старой теме
        $service = new MailBoxService();

        if (0 < $forwardSubjectId) {
            $characterThemeModel = MailCharacterThemesModel::model()
                ->byCharacter($receiverId)
                ->byTheme($forwardSubjectId)->find();
            if ($characterThemeModel) {
                $characterThemeId = $characterThemeModel->id;
                if ($characterThemeModel->constructor_number === 'TXT') {
                    $result['text'] = $characterThemeModel->letter->message;
                } else {
                    $result['phrases']['data'] = MailBoxService::getMailPhrases($characterThemeId);
                    $result['subjectId'] = $characterThemeId; //$subjectId;
                }
            }
        }

        if (!isset($result['phrases']) && !isset($result['text'])) {
            $result['phrases']['data'] = MailBoxService::getMailPhrases();
        }  // берем дефолтные
        $result['phrases']['addData'] = MailBoxService::getSigns();


        $result['result']    = 1;
        $result['subject']   = $forwardSubjectText;
        $result['subjectId'] = $forwardSubjectId;

        $result['phrases']['previouseMessage'] = $messageToForward->message;

        return $result;
    }
}


