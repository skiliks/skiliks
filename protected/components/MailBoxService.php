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
        foreach($folders as $folder) {
            $id = (int)$folder->id;
            $list[$id] = array(
                'id' => $id,
                'name' => $folder->name,
                'unreaded' => 0
            );
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
        foreach($messages as $message) {
            
            
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
            
            
            
            $item = array(
                'id' => $message->id,
                'subject' => $subject,
                //'message' => $message->message,
                'sendingDate' => DateHelper::toString($message->sending_date),
                'receivingDate' => DateHelper::toString($message->receiving_date),
                'sender' => $senderId,
                'receiver' => $message->receiver_id,
                'readed' => $message->readed
            );
            //if ($order == 'subject') {
                $item['subjectSort'] = $this->processSubject($subject);
            //}
            
            $list[] = $item;
        }
        
        // проставляем имена персонажей
        $characters = $this->getCharacters($users);
        foreach($list as $index=>$item) {
            $list[$index]['sender'] = $characters[$list[$index]['sender']];
            $list[$index]['receiver'] = $characters[$list[$index]['receiver']];
        }
        
        if ($orderType == 'ASC') $ordeFlag = SORT_ASC;
        else $ordeFlag = SORT_DESC;
        
        
        // подготовка для сортировки на уровне php
        $receivers = array();
        foreach ($list as $key => $row) {
           $subjects[$key]  = $row['subjectSort'];
           $senders[$key] = $row['sender'];
           $receivers[$key] = $row['receiver'];
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
        
        return $message;
    }
    
    public function sendMessage($params) {
        Logger::debug("sendMessage");
        
        $letterType = false;
        if (isset($params['letterType'])) $letterType = $params['letterType'];
        
        if ($letterType == 'forward') {
            $subject_id = $params['subject']; // костыль!
        }
        else {
            // определение темы
            $model = MailCharacterThemesModel::model()->byId($params['subject'])->find();
            if (!$model) throw new Exception("cant get model by id {$params['subject']}");
            $subject_id = $model->theme_id;
        }
        
        
        
        
        $receivers = explode(',', $params['receivers']);
        $receiverId = (int)$receivers[0];
        
        Logger::debug("simId : {$params['simId']} group_id : {$params['group']} sender_id : {$params['sender']} subject_id : $subject_id");
        
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
    
    public function getMailPhrases($ids) {
        $phrases = MailPhrasesModel::model()->byCharacterThemes($ids)->findAll();
        
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
        //if (count($receivers) == 2 && isset($receivers[1]) && $receivers[1]=='') unset($receivers[1]);
        
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
                
                if (!isset($themes[$collection[$index]['id']])) {
                    $themes[$collection[$index]['id']] = $collection[$index]['theme_id'];
                    $processedItems++;
                }
            }
        }
        
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
     * Копирование шаблонов писем в рамках заданной симуляции
     * @param int $simId 
     */
    public static function copyTemplates($simId) {
        Logger::debug("copyTemplates : $simId");
        $connection = Yii::app()->db;
        $sql = "insert into mail_box 
            (sim_id, template_id, group_id, sender_id, receiver_id, subject, sending_date, receiving_date, message, subject_id)
            select :simId, id, group_id, sender_id, receiver_id, subject, sending_date, receiving_date, message, subject_id
            from mail_template";
        
        $command = $connection->createCommand($sql);     
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $command->execute();
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
        return $model->template_id; 
    }
    
    public static function getTasks($templateId) {
        $collection = MailTasksModel::model()->byMailId($templateId)->findAll();
        
        $tasks = array();
        foreach($collection as $task) {
            $tasks[] = array(
                'id' => $task->id,
                'name' => $task->name
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
