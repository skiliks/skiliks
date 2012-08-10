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
            $list[] = array(
                'id' => $folder->id,
                'name' => $folder->name
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
        $model->byReceiver($receiverId)->byFolder($folderId);
        if ($order) $model->orderBy($order, $orderType);
        $messages = $model->findAll();
        
       
        $users = array();
        $list = array();
        foreach($messages as $message) {
            $senderId = (int)$message->sender_id;
            $receiverId = (int)$message->receiver_id;
            $users[$senderId] = $senderId;
            $users[$receiverId] = $receiverId;
            
            $list[] = array(
                'id' => $message->id,
                'subject' => $message->subject,
                //'message' => $message->message,
                'sendingDate' => DateHelper::toString($message->sending_date),
                'receivingDate' => DateHelper::toString($message->receiving_date),
                'sender' => $senderId,
                'receiver' => $message->receiver_id
            );
            
        }
        // @todo: только фио
        $characters = $this->getCharacters($users);

        foreach($list as $index=>$item) {
            $list[$index]['sender'] = $characters[$list[$index]['sender']];
            $list[$index]['receiver'] = $characters[$list[$index]['receiver']];
        }
        
        return $list;
    }
    
    /**
     * Загрузка одиночного сообщения
     * @param type $id 
     */
    public function getMessage($id) {
        $model = MailBoxModel::model()->byId($id)->find();
        $message = array(
            'id' => $model->id,
            'subject' => $model->subject,
            'message' => $model->message,
            'sendingDate' => DateHelper::toString($model->sending_date),
            'receivingDate' => DateHelper::toString($model->receiving_date),
            'sender' => $model->sender_id,
            'receiver' => $model->receiver_id
        );
        
        $characters = $this->getCharacters(array($model->sender_id, $model->receiver_id));
        $message['sender'] = $characters[$message['sender']];
        $message['receiver'] = $characters[$message['receiver']];
        
        return $message;
    }
    
    public function sendMessage($params) {
        $model = new MailBoxModel();
        $model->group_id = $params['group'];
        $model->sender_id = $params['sender'];
        $model->receiver_id = $params['receiver'];
        $model->subject = $params['subject'];
        //$model->message = $params['message'];
        $model->sending_date = time();
        $model->insert();
        
        $mailId = $model->id;
        
        if (isset($params['receivers'])) {
            $this->saveCopies($params['receivers'], $mailId);
        }
        
        if (isset($params['message'])) {
            $phrases = explode(',', $params['message']);
            foreach($phrases as $phraseId) {
                $model = new MailMessagesModel();
                $model->mail_id = $mailId;
                $model->phrase_id = $phraseId;        
                $model->insert();
            }
        }
        
        
        //MailPhrasesModel
    }
    
    public function saveCopies($receivers, $mailId) {
        $receivers = explode(',', $receivers);
        foreach($receivers as $receiverId) {
            $model = new MailCopiesModel();
            $model->mail_id = $mailId;
            $model->receiver_id = $receiverId;
            $model->insert();
        }
    }
    
    public function getMailPhrases() {
        $phrases = MailPhrasesModel::model()->findAll();
        
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
    }
}

?>
