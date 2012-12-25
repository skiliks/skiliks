<?php



/**
 * Description of MailBoxService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailBoxService {
    
    /**
     * Получить список папок
     * @return type 
     */
    public function getFolders() {
        $folders = MailFoldersModel::model()->findAll(array('limit'=>4));
        
        $list = array();
        $index = 1;
        foreach($folders as $folder) {
            $id = (int)$folder->id;
            $list[$id] = array(
                'id' => $id,
                'name' => $folder->name,
                'unreaded' => 0
            );
            $index++;
        }
        return $list;
    }
    
    /**
     * Загрузка персонажей
     * 
     * @param array $ids 
     * 
     * @return array
     */
    public function getCharacters($ids = array()) 
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
    
    protected function processSubject($subject) {
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
     * Получение списка собщений
     * @param $params
     * @internal param int $folderId
     * @internal param int $receiverId
     * @return array
     */
    public function getMessages($params) 
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
        if ($order == 'time') $orderField = 'receiving_date';
        
        $orderType = (isset($params['orderType'])) ? $params['orderType'] : false;
        if ($orderType == 0) $orderType = 'ASC';
        else $orderType = 'DESC';
        
        $model = MailBoxModel::model();
        $model->bySimulation($params['simId']);
        
        if ($folderId == 3) { // исходящие
            //$model->bySender($params['uid']);
        }
        else {
            //$model->byReceiver($receiverId);
        }
        
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
            
            $subject = $message->subject;
            
            
            if ($subject == '') {
                $subject = 'Broken value.';
                if ($message->subject_id > 0) {
                    // we store both mail_tehame and mail_character_theme in mail_box
                    $subjectModel = MailThemesModel::model()->byId($message->subject_id)->find();
                    if (null !== $subjectModel && ($subjectModel->sim_id == $simId || $subjectModel->sim_id === null)) {
                        $subject = $subjectModel->name; // CASE 1
                    } else {
                        $subjectModel = MailCharacterThemesModel::model()->byId($message->subject_id)->find();
                        if (null !== $subjectModel) {
                            $id = $subjectModel->theme_id;
                            $subjectModel = MailThemesModel::model()->byId($id)->find();
                            if (null !== $subjectModel) {
                                $subject = $subjectModel->name; // CASE 2
                            } 
                        }
                    }
                }
            }

            
            $readed = $message->readed;
            // Для черновиков и исходящих письма всегда прочитаны - fix issue 69
            if ($folderId == 2 || $folderId == 3) $readed = 1;
            
            $item = array(
                'id' => $message->id,
                'subject' => $subject,
                //'message' => $message->message,
                'sendingDate' => date("d.m.Y", $message->sending_date)." ".date("H:i:s",$message->sending_time),
                'sendingDateInt' => $message->sending_date,
                'receivingDate' => DateHelper::toString($message->sending_date), //DateHelper::toString($message->receiving_date),
                'receivingDateInt' => $message->sending_date, //$message->receiving_date,
                'sender' => $senderId,
                'receiver' => $message->receiver_id,
                'readed' => $readed,
                'attachments' => 0
            );
            //if ($order == 'subject') {
                $item['subjectSort'] = $this->processSubject($subject);
            //}
            
            $list[(int)$message->id] = $item;
        }
        
        // проставляем имена персонажей
        $characters = $this->getCharacters($users);
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
           
           $receivingDate[$key] = $row['receivingDateInt'];
           $sendingDate[$key] = $row['sendingDateInt'];
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
        
        if ($order == 'time') {
            if ($folderId == 3) {  //исходящие
                array_multisort($sendingDate, $ordeFlag,  $list);
            }
            else 
                array_multisort($receivingDate, $ordeFlag,  $list);
        }
        
        return $list;
    }
    
    /**
     * Загрузка одиночного сообщения
     * @param type $id 
     */
    public function getMessage($id) {
        $model = MailBoxModel::model()->byId($id)->find();
        if (!$model) return array();
        
        $simId = $model->sim_id;
        
        $subject = $model->subject;
        if ($subject == '') {
            if ($model->subject_id > 0) {
                $subjectModel = MailThemesModel::model()->byId($model->subject_id)->find();
                if ($subjectModel) {
                    $subject = $subjectModel->name;
                }
            }
        }
        
        $message = array(
            'id' => $model->id,
            'subject' => $subject,
            'message' => $model->message,
            'sendingDate' => DateHelper::toString($model->sending_date),
            'receivingDate' => DateHelper::toString($model->receiving_date),
            'sender' => $model->sender_id,
            'receiver' => $model->receiver_id
        );
        $message_id = $model->message_id;

        // array($model->sender_id, $model->receiver_id)
        // Получим всех персонажей
        $characters = $this->getCharacters();
        
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
            $message['message'] = $this->buildMessage($model->id);
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
    public function sendMessage($params) {

        $subject_id = $params['subject_id'];
        $subject = $params['subject'];
        assert($subject !== null || $subject_id !== null);
        $message_id = $params['message_id'];
        assert($message_id !== null);

        $letterType = false;
        if (isset($params['letterType'])) $letterType = $params['letterType'];
        
        $emailSubject = MailThemesModel::model()->findByPk($subject_id);
        
        $receivers = explode(',', $params['receivers']);
        $receiverId = (int)$receivers[0];
        
        $subject_id = MailThemesModel::model()->getSubjectId($subject_id, $message_id);

        $message = new MailBoxModel();
        $message->group_id = $params['group'];
        $message->sender_id = $params['sender'];
        $message->subject_id = $subject_id;
        $message->subject = $subject;
        $message->receiver_id = $receiverId;
        $message->sending_date = gmmktime(0, 0, 0, 10, 4, 2012);

        $message->sending_time = $params['timeString'];        
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
                $this->saveCopies($params['copies'], $mailId);
        }
        
        if (isset($params['receivers'])) {
            if (count($receivers)>1)
                $this->saveReceivers($receivers, $mailId);
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
        
        $logs = array(array(10,13,0,$params['timeString'], array('mailId'=>$mailId)));
        LogHelper::setMailLog($params['simId'], $logs);
        
        return $message;
    }
    
    
    
    public function saveCopies($receivers, $mailId) {
        $receivers = explode(',', $receivers);
        if ($receivers[count($receivers)-1] == '') unset($receivers[count($receivers)-1]);
        
        foreach($receivers as $receiverId) {
            $model = new MailCopiesModel();
            $model->mail_id = $mailId;
            $model->receiver_id = $receiverId;
            $model->insert();
        }
    }
    
    public function getMailPhrases($id = false) {
        $phrases = array();
        
        if ($id) {
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
    
    public function getSigns() {
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
    public function buildMessage($mailId) {
        $mail = MailBoxModel::model()->findByPk($mailId);
         $characterTheme = $mail->getCharacterTheme();
        if ($characterTheme && $characterTheme->constructor_number == 'TXT') {
            return $characterTheme->letter->message;
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
    public function saveReceivers($receivers, $mailId) {
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
    public function getThemes($receivers) {
        
        
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
                $themes[(int)$model->id] = (int)$model->theme_id;
            }
        }
        
        // если у нас более одного получателя
        if (count($receivers) > 1) {
            $models = MailCharacterThemesModel::model()->byMail()->findAll();
            $collection = array();
            foreach($models as $model) {
                $collection[] = array(
                    'id' => (int)$model->id,
                    'theme_id' => (int)$model->theme_id
                );
            }
            
            $processedItems = 0;
            while($processedItems < 10) {
                $index = rand(0, 10);
                
                if (isset($collection[$index]))
                if (!isset($themes[$collection[$index]['id']])) {
                    $themes[$collection[$index]['id']] = $collection[$index]['theme_id'];
                    
                }
                $processedItems++;
            }
        }
        
        if (count($themes) == 0) return array();
        
        //var_dump($themes);die();
        
        // загрузка тем
        $themeCollection = MailThemesModel::model()->byIds($themes)->findAll();
        $captions = array();
        foreach($themeCollection as $themeModel) {
            $captions[(int)$themeModel->id] = $themeModel->name;
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
    
    public function delete($id) {
        $model = MailBoxModel::model()->byId($id)->find();
        $model->group_id = 4;
        $model->save();
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
            (sim_id, template_id, group_id, sender_id, receiver_id, subject, sending_date, receiving_date, message, subject_id, code, type)
            select :simId, id, group_id, sender_id, receiver_id, subject, sending_date, receiving_date, message, subject_id, code, type
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
        $receivingDate = time();
        $sql = "insert into mail_box 
            (sim_id, template_id, group_id, sender_id, receiver_id, subject, sending_date, receiving_date, message, subject_id, code, sending_time, type)
            select :simId, id, group_id, sender_id, receiver_id, subject, sending_date, $receivingDate, message, subject_id, code, sending_time, type
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
    
    public function setAsReaded($id) {
        $model = MailBoxModel::model()->byId($id)->find();
        if (!$model) return false;
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
        $subjectModel = MailThemesModel::model()->byId($subjectId)->find();
        if ($subjectModel) {
            return $subjectModel->name;
        }
        
        return false;
    }
    
    /**
     * Создает тему
     * 
     * @param string $subject
     * @param int $simId
     * @return int
     */
    public static function createSubject($subject, $simId) {
        $subjectModel = new MailThemesModel();
        $subjectModel->name = $subject;
        $subjectModel->sim_id = $simId;
        $subjectModel->insert();
        return $subjectModel->id;
    }
    
    public static function getSubjectIdByName($subject)
    {
        $model = MailThemesModel::model()->byName($subject)->bySimIdNull()->find();
        
        if (!$model) {
            return false;
        }
        
        return $model->id;
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
            
        return $result;
    }
}


