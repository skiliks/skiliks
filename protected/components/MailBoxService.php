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
        $folders = MailFoldersModel::model()->findAll();
        
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
     * @param array $ids 
     * @return array
     */
    public function getCharacters($ids=array()) {
        $model = Characters::model();
        if (count($ids)>0) $model->byIds($ids);
        $charactersCollection = $model->findAll();
        
        $characters = array();
        foreach($charactersCollection as $characterModel) {
            $characters[$characterModel->id] = $characterModel->fio.' <'.$characterModel->email.'>';
        }
        
        return $characters;
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
     * @param int $folderId
     * @param int $receiverId
     * @return array
     */
    public function getMessages($params) {
        
        //var_dump($params);
        $folderId = $params['folderId'];
        $receiverId = $params['receiverId'];
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
        
        
       
        //var_dump($model->getCommandBuilder()); die();
        
        
        //Logger::debug("message : ".var_export($messages, true));
       
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
                if ($message->subject_id > 0) {
                    $subjectModel = MailThemesModel::model()->byId($message->subject_id)->find();
                    if ($subjectModel) {
                        $subject = $subjectModel->name;
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
                'sendingDate' => DateHelper::toString($message->sending_date + $message->sending_time*60),
                'sendingDateInt' => $message->sending_date,
                'receivingDate' => DateHelper::toString($message->sending_date + $message->sending_time*60), //DateHelper::toString($message->receiving_date),
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

        Logger::debug("receivers : ".var_export($receivers, true));
        
        if ($order == 'subject') {
            array_multisort($subjects, $ordeFlag,  $list);
        }
        
        if ($order == 'sender') {
            array_multisort($senders, $ordeFlag,  $list);
            //Logger::debug("after sortinf senders : ".var_export($senders, true));
        }
        
        if ($order == 'receiver') {
            array_multisort($receivers, $ordeFlag,  $list);
            Logger::debug("after sortinf receivers : ".var_export($receivers, true));
        }
        
        if ($order == 'time') {
            if ($folderId == 3) {  //исходящие
                array_multisort($sendingDate, $ordeFlag,  $list);
            }
            else 
                array_multisort($receivingDate, $ordeFlag,  $list);
        }
        
        
        //ksort($list);
        //var_dump($list); die();
        return $list;
    }
    
    /**
     * Загрузка одиночного сообщения
     * @param type $id 
     */
    public function getMessage($id) {
        $model = MailBoxModel::model()->byId($id)->find();
        if (!$model) return array();
        
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
        
        // array($model->sender_id, $model->receiver_id)
        // Получим всех персонажей
        $characters = $this->getCharacters();
        
        // загрузим ка получателей
        $receivers = MailReceiversModel::model()->byMailId($id)->findAll();
        //var_dump($receivers);
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
        //$message['receiver'] = $characters[$message['receiver']];
        
        // Собираем сообщение
        if ($message['message'] == '') {
            $message['message'] = $this->buildMessage($model->id);
        }
        
        $message['attachments'] = MailAttachmentsService::get($model->id);
        
        return $message;
    }
    
    public function sendMessage($params) {
        Logger::debug("sendMessage");
        
        $subject_id = $params['subject'];
        
        $letterType = false;
        if (isset($params['letterType'])) $letterType = $params['letterType'];
        
        if ($letterType == 'forward') {
            $subject_id = $params['subject']; // костыль!
        }
        else {
            // определение темы
            $model = MailCharacterThemesModel::model()->byId($params['subject'])->find();
            //if (!$model) throw new Exception("cant get model by id {$params['subject']}");
            if ($model)
                $subject_id = $model->theme_id;
        }
        
        
        
        
        $receivers = explode(',', $params['receivers']);
        $receiverId = (int)$receivers[0];
        
        Logger::debug("insert mail : ".var_export($params));
        Logger::debug("insert mail : subject = $subject_id receiver = $receiverId");
        
        $model = new MailBoxModel();
        $model->group_id = $params['group'];
        $model->sender_id = $params['sender'];
        $model->subject_id = $subject_id;
        $model->receiver_id = $receiverId;
        //$model->subject = $params['subject'];
        //$model->message = $params['message'];
        $model->sending_date = time();
        $model->readed = 0;
        $model->sim_id = $params['simId'];
        $model->insert();
        
        $mailId = $model->id;

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
            if ($phrases[count($phrases)-1] == '') unset($phrases[count($phrases)-1]);
            
            //Logger::debug("phrases : ".var_export($params['phrases'], true));
            
            foreach($phrases as $phraseId) {
                //Logger::debug("insert : mailId $mailId phraseId $phraseId");
                
                $model = new MailMessagesModel();
                $model->mail_id = $mailId;
                $model->phrase_id = $phraseId;        
                $model->insert();
            }
        }
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
                Logger::debug("get mail phrases by code: $constructorNumber");
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
    public static function initDefaultSettings($simId) {
        $model = new MailSettingsModel();
        $model->sim_id = $simId;
        $model->insert();
        
        self::copyTemplates($simId);
        
        return true;
        
        // copy mail tasks
        $sql = 'SELECT e.id, e.trigger_time,
                e.code , e.code REGEXP "^M[[:digit:]]+$"  as code2, m.sending_date_str, m.sending_time_str
            FROM `events_samples` as e  
            inner join mail_template as m on (m.code = e.code and m.sending_date=1349308800 and sending_time>0)
            where e.code REGEXP "^M[[:digit:]]+$" =1 
            and e.trigger_time>0 
            order by sending_time_str';
        $command = Yii::app()->db->createCommand($sql);
        $events = $command->queryAll();
        
        foreach($events as $event) {
            Logger::debug("check mail event : ".var_export($event, true));
            
            $eventsTriggers = EventsTriggers::model()->bySimIdAndEventId($simId, $event['id'])->find();
            if ($eventsTriggers) continue; // у нас уже есть такое событие
            
            Logger::debug("create mail event : {$event['code']}");
            $eventsTriggers = new EventsTriggers();
            $eventsTriggers->sim_id         = $simId;
            $eventsTriggers->event_id       = $event['id'];
            $eventsTriggers->trigger_time   = $event['trigger_time']; 
            $eventsTriggers->save();
        }
    }
    
    /**
     * Сборка сообщения
     * @param int $mailId 
     * @return string
     */
    public function buildMessage($mailId) {
        $models = MailMessagesModel::model()->byMail($mailId)->findAll();
        
        $phrases = array();
        foreach($models as $model) {
            $phrases[] = $model->phrase_id;
        }
        
        Logger::debug("phrases : ".var_export($phrases, true));
        // получение набора фраз
        $phrasesCollection = MailPhrasesModel::model()->byIds($phrases)->findAll();
        
        $phrasesDictionary = array();
        foreach($phrasesCollection as $phraseModel) {
            $phrasesDictionary[$phraseModel->id] = $phraseModel->name;
        }
        Logger::debug("phrasesDictionary : ".var_export($phrasesDictionary, true));
        
        $collection = array();
        foreach($phrases as $index => $phraseId) {
            $collection[] = $phrasesDictionary[$phraseId];
        }
        
        // склейка фраз
        return implode(' ', $collection);
    }
    
    /**
     * Сохранить получателей сообщения
     * @param string $receivers 
     */
    public function saveReceivers($receivers, $mailId) {
        //$receivers = explode(',', $receivers);
        Logger::debug("receivers ".var_export($receivers, true));
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
        //if (count($receivers) == 2 && isset($receivers[1]) && $receivers[1]=='') unset($receivers[1]);
        
        Logger::debug("receivers : ".var_export($receivers, true));
        Logger::debug("count receivers : ".count($receivers));
        
        $themes = array();
        if (count($receivers) == 1) {
            // загрузка тем по одному персонажу
            $models = MailCharacterThemesModel::model()->byCharacter($receivers[0])->findAll();
            
            foreach($models as $model) {
                $themes[(int)$model->id] = (int)$model->theme_id;
            }
        }
        
        // если у нас более одного получателя
        if (count($receivers) > 1) {
            $models = MailCharacterThemesModel::model()->findAll();
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
        //var_dump($captions);die();
        
        foreach($themes as $id=>$themeId) {
            //var_dump($themes[$id]);
            $themes[$id] = $captions[$themeId];
        }
        
        //var_dump($themes);die();
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
            (sim_id, template_id, group_id, sender_id, receiver_id, subject, sending_date, receiving_date, message, subject_id, code)
            select :simId, id, group_id, sender_id, receiver_id, subject, sending_date, receiving_date, message, subject_id, code
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
        Logger::debug("try to add attachment for mail:  $templateId");
        $command = $connection->createCommand($sql);     
        $command->bindParam(":mailId", $templateId, PDO::PARAM_INT);
        $row = $command->queryRow();
        Logger::debug("row = ".var_export($row, true));
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
            
            //Logger::debug("file = ".var_export($file, true));
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
    public static function copyTemplates($simId) {
        Logger::debug("copyTemplates : $simId");
        $connection = Yii::app()->db;
        $receivingDate = time();
        $sql = "insert into mail_box 
            (sim_id, template_id, group_id, sender_id, receiver_id, subject, sending_date, receiving_date, message, subject_id, code, sending_time)
            select :simId, id, group_id, sender_id, receiver_id, subject, sending_date, $receivingDate, message, subject_id, code, sending_time
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
                WHERE sim_id = :simId AND readed = 0
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
    
    public static function getSubjectIdByName($subject) {
        $model = MailThemesModel::model()->byName($subject)->find();
        if (!$model) return false;
        return $model->id;
    }
}

?>
