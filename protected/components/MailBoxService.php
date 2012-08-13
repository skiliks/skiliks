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
    
    /**
     * Получение списка собщений
     * @param int $folderId
     * @param int $receiverId
     * @return array
     */
    public function getMessages($params) {
        
        $folderId = $params['folderId'];
        $receiverId = $params['receiverId'];
        $order = (isset($params['order'])) ? $params['order'] : false;
        if ($order == -1) {
            $order = false;
        }
        
        if ($order == 'sender') $order = 'sender_id';
        if ($order == 'time') $order = 'receiving_date';
        
        $orderType = (isset($params['orderType'])) ? $params['orderType'] : false;
        if ($orderType == 0) $orderType = 'ASC';
        else $orderType = 'DESC';
        
        $model = MailBoxModel::model();
        if ($folderId == 3) { // исходящие
            $model->bySender($params['uid']);
        }
        else {
            $model->byReceiver($receiverId);
        }
        
        $model->byFolder($folderId);
        if ($order) $model->orderBy($order, $orderType);
        $messages = $model->findAll();
       
        //var_dump($messages); die();
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
            
            $list[] = array(
                'id' => $message->id,
                'subject' => $subject,
                //'message' => $message->message,
                'sendingDate' => DateHelper::toString($message->sending_date),
                'receivingDate' => DateHelper::toString($message->receiving_date),
                'sender' => $senderId,
                'receiver' => $message->receiver_id,
                'readed' => $message->readed
            );
            
        }
        // @todo: только фио
        $characters = $this->getCharacters($users);
        
        foreach($list as $index=>$item) {
            $list[$index]['sender'] = $characters[$list[$index]['sender']];
            $list[$index]['receiver'] = $characters[$list[$index]['receiver']];
        }
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
        
        $receiversCollection = array();
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
        // определение темы
        $model = MailCharacterThemesModel::model()->byId($params['subject'])->find();
        if (!$model)  throw new Exception("cant get model by id {$params['subject']}");
        
        $subject_id = $model->theme_id;
        
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
            if (count($params['receivers'])>1)
                $this->saveReceivers($params['receivers'], $mailId);
        }
        
        // Сохранение фраз
        if (isset($params['phrases'])) {
            $phrases = explode(',', $params['phrases']);
            if ($phrases[count($phrases)-1] == '') unset($phrases[count($phrases)-1]);
            foreach($phrases as $phraseId) {
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
        
        // получение набора фраз
        $phrasesCollection = MailPhrasesModel::model()->byIds($phrases)->findAll();
        
        $phrases = array();
        foreach($phrasesCollection as $phraseModel) {
            $phrases[] = $phraseModel->name;
        }
        
        // склейка фраз
        return implode(' ', $phrases);
    }
    
    /**
     * Сохранить получателей сообщения
     * @param string $receivers 
     */
    public function saveReceivers($receivers, $mailId) {
        $receivers = explode(',', $receivers);
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
            (sim_id, group_id, sender_id, receiver_id, subject, sending_date, receiving_date, message)
            select :simId, group_id, sender_id, receiver_id, subject, sending_date, receiving_date, message
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
        
        return $folders;
    }
}

?>
